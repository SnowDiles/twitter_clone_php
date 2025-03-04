import { handleAutoCompletion } from "./autoCompletion.js";
/**
 * Class representing a tweet posting functionality
 * @class
 */
class TweetPost {
    /**
     * Creates an instance of TweetPost and initializes DOM elements
     * @constructor
     */
    constructor() {
        this.tweetContentTextarea = document.getElementById('post-text-area-desktop');
        this.submitTweetButton = document.getElementById('post-button-desktop');
        this.mediaUploadButton = document.getElementById('upload-button-desktop');
        this.mediaFileInput = document.getElementById('file-input-desktop');

        this.mobileTweetContentTextarea = document.getElementById('post-text-area-mobile');
        this.mobileSubmitTweetButton = document.getElementById('post-button-mobile');
        this.mobileMediaUploadButton = document.getElementById('upload-button-mobile');
        this.mobileMediaFileInput = document.getElementById('file-input-mobile');

        this.mobileModal = document.getElementById('mobile-modal');
        this.btnMobileModal = document.getElementById('btn-mobile-modale');
        this.btnCloseMobileModal = document.getElementById('close-mobile-modal');
        this.initializeEventListeners();
    }

    /**
     * Initializes event listeners for tweet posting functionality
     * @private
     */
    initializeEventListeners() {
        this.tweetContentTextarea.addEventListener('input', () => this.updateSubmitButtonState());
        this.mediaUploadButton.addEventListener('click', () => this.triggerFileUpload());
        this.mediaFileInput.addEventListener('change', (event) => this.handleMediaSelection(event, 'desktop'));
        this.submitTweetButton.addEventListener('click', () => this.submitTweet());

        this.mobileTweetContentTextarea?.addEventListener('input', () => this.updateSubmitButtonState('mobile'));
        this.mobileMediaUploadButton?.addEventListener('click', () => this.triggerFileUpload('mobile'));
        this.mobileMediaFileInput?.addEventListener('change', (event) => this.handleMediaSelection(event, 'mobile'));
        this.mobileSubmitTweetButton?.addEventListener('click', () => this.submitTweet('mobile'));

        this.btnMobileModal.addEventListener('click', () => this.openMobileModal());
        this.btnCloseMobileModal.addEventListener('click', () => this.closeMobileModal());
    }


    /**
     * Close the mobile modal for tweet posting
     * @private
     */
    closeMobileModal() {
        if (this.mobileModal) {
            this.mobileModal.classList.add('hidden');
            this.mobileModal.classList.remove('flex')
        }
    }

    /**
     * Opens the mobile modal for tweet posting
     * @private
     */
    openMobileModal() {
        if (this.mobileModal) {
            this.mobileModal.classList.remove('hidden');
            this.mobileModal.classList.add('flex')
        }
    }

    /**
     * Handles tweet submission with or without media
     * @private
     */
    submitTweet(view) {
        const textarea = view === 'mobile' ? this.mobileTweetContentTextarea : this.tweetContentTextarea;
        const fileInput = view === 'mobile' ? this.mobileMediaFileInput : this.mediaFileInput;

        const tweetContent = textarea.value;
        const formData = new FormData();

        formData.append('content', tweetContent);

        if (this.isValidTweetLength(tweetContent)) {
            const mediaFile = fileInput.files[0];
            if (mediaFile) {
                formData.append('image', mediaFile);
                formData.append('action', 'addPostsMedia');
            } else {
                formData.append('action', 'addPosts');
            }

            this.submitTweetMedia(formData, view);
        } else {
            alert("Message trop long");
        }
    }

    /**
     * Submits a tweet 
     * @param {FormData} formData - Form data containing tweet content
     * @returns {Promise<void>}
     * @private
     * @async
     */
    async submitTweetMedia(formData, view) {
        try {
            const response = await fetch("../../src/Controllers/HomeController.php", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new TypeError("La réponse n'est pas au format JSON!");
            }

            const responseData = await response.json();

            if (responseData.success) {
                this.clearTextArea(view);
                if (view === 'mobile') {
                    this.closeMobileModal();
                }
                const desktopContainer = document.getElementById('tweets-container');
                const mobileContainer = document.querySelector('.feed.md\\:invisible');
                if (desktopContainer) desktopContainer.innerHTML = '';
                if (mobileContainer) mobileContainer.innerHTML = '';

                new TweetFeed();

            } else {
                console.error("Erreur serveur:", responseData.message);
                alert(responseData.message || "Une erreur est survenue");
            }
        } catch (error) {
            console.error("Erreur lors de l'envoi du tweet:", error);
            alert("Une erreur est survenue lors de l'envoi du tweet");
        }
    }

    /**
     * Clears the tweet textarea and removes image preview
     * @returns {boolean} True if clearing was successful
     */
    clearTextArea(view) {
        const textarea = view === 'mobile' ? this.mobileTweetContentTextarea : this.tweetContentTextarea;
        textarea.value = '';

        const existingImageContainer = document.querySelector(
            view === 'mobile' ? '#mobile-modal .image-preview-container' : '.image-preview-container'
        );
        if (existingImageContainer) {
            existingImageContainer.remove();
        }
        return true;
    }

    /**
     * Validates the length of tweet content
     * @param {string} content - The tweet content to validate
     * @returns {boolean} True if content length is valid (0-140 characters)
     * @private
     */
    isValidTweetLength(content) {
        return content.length <= 140 && content.length >= 0;
    }

    /**
     * Updates submit button state based on content and media presence
     * @private
     */
    updateSubmitButtonState() {
        const hasContent = this.tweetContentTextarea.value.trim() !== '';
        const hasMediaFile = this.mediaFileInput.files.length > 0;
        this.submitTweetButton.disabled = !hasContent && !hasMediaFile;
    }

    /**
     * Triggers the file upload dialog
     * @private
     */
    triggerFileUpload(view) {
        const fileInput = view === 'mobile' ? this.mobileMediaFileInput : this.mediaFileInput;
        fileInput.click();
    }

    /**
     * Handles media file selection and preview
     * @param {Event} event - File input change event
     * @private
     */
    handleMediaSelection(event, view) {
        const mediaFile = event.target.files[0];
        if (mediaFile && mediaFile.type.startsWith('image/')) {
            this.selectedMediaFile = mediaFile;
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewImage = document.createElement('img');
                previewImage.src = e.target.result;
                previewImage.className = 'max-w-full h-auto rounded-lg mt-2 mb-2 max-h-[200px] object-contain';

                const existingImageContainer = document.querySelector(
                    view === 'mobile' ? '#mobile-modal .image-preview-container' : '.image-preview-container'
                );
                if (existingImageContainer) {
                    existingImageContainer.remove();
                }

                const imageContainer = document.createElement('div');
                imageContainer.className = 'image-preview-container w-full flex justify-center items-center';
                imageContainer.appendChild(previewImage);

                if (view === 'mobile') {
                    const mobileContainer = document.querySelector('#mobile-modal .flex-1');
                    if (mobileContainer) {
                        const textareaContainer = mobileContainer.querySelector('.flex.gap-4');
                        if (textareaContainer) {
                            mobileContainer.insertBefore(imageContainer, textareaContainer.nextSibling);
                        }
                    }
                } else {
                    const desktopContainer = this.tweetContentTextarea.closest('.flex-grow.flex.flex-col.gap-4') ||
                        this.tweetContentTextarea.closest('.bg-white.dark\\:bg-gray-800');

                    if (desktopContainer) {
                        const insertBeforeElement = desktopContainer.querySelector('.flex.justify-between') ||
                            desktopContainer.lastElementChild;
                        desktopContainer.insertBefore(imageContainer, insertBeforeElement);
                    }
                }
            };
            reader.readAsDataURL(mediaFile);
            this.updateSubmitButtonState(view);
        } else {
            alert("Veuillez sélectionner une image valide");
        }
    }
}
class TweetFeed {
    constructor() {
        this.isLoading = false;
        this.desktopTweetsContainer = document.getElementById('tweets-container');
        this.mobileTweetsContainer = document.querySelector('.feed.md\\:invisible');
        this.loadingElement = document.getElementById('loading');

        this.loadTweets();
    }

    async createTweetElement(tweet) {
        if (tweet.message) {
            return `
            <div class="p-4 max-w-xl">
                <div class="flex justify-center items-center gap-3">
                    ${tweet.message}
                </div>
            </div>
            `;
        }

        const tweetContentMentions = tweet.content.slice(tweet.content.search('@'));
        const mentions = tweetContentMentions.match(/@[a-zA-Z0-9_]+/g);

        const mentionResult = await this.checkMention(mentions);

        if (mentionResult.success && mentionResult.userId) {
            const createLinkElement = document.createElement("a");
            createLinkElement.href = `./UserController.php?userId=${mentionResult.userId}`;
            createLinkElement.textContent = mentions[0];
            createLinkElement.classList.add('text-primary-500');
            tweet.content = tweet.content.replace(mentions[0], createLinkElement.outerHTML);
        }
        const tweetContentHashtag = tweet.content.slice(tweet.content.search('#'));
        const hashtags = tweetContentHashtag.match(/#[a-zA-Z0-9_]+/g);
        if (hashtags) {
            const createLinkElement = document.createElement("a");
            createLinkElement.href = `/explore/${hashtags[0].trim().substring(1)}`;
            createLinkElement.textContent = hashtags[0];
            createLinkElement.classList.add('text-primary-500');
            tweet.content = tweet.content.replace(hashtags[0], createLinkElement.outerHTML);
        }

        return `
            <div class="p-4 max-w-xl">
                <div class="flex gap-3">
                    <div class="w-12 h-12">
                        <img src="../../assets/icons/profile.png" alt="profile" class="invert dark:invert-0 w-full h-full rounded-full">
                    </div>
                    <div>
                    <div class="flex items-center gap-2">
                        <a class="text-xl" href="./UserController.php?userId=${tweet.user_id}">${tweet.username}</a>
                        <a class="text-tertiary-500" href="./UserController.php?userId=${tweet.user_id}">@${tweet.handle}</a>
                        <span class="text-xs">•</span>
                        <span class="">${tweet.date}</span>
                    </div>
                    <div class="ml-0 mt-3">
                        <div class="text-small text-xl">
                            ${tweet.content}
                        </div>
                        ${tweet.image_url ? `
                        <a href="${tweet.image_url}" class="mt-3 mb-3 block h-[300px] w-[300px] overflow-hidden" target="_blank">
                        <img src="${tweet.image_url}" alt="Tweet media" class="w-full h-full object-cover rounded-lg">
                        </a>
                        ` : ''}
                        <div class="flex items-center gap-4 mt-2">
                        <button class="flex items-center">
                        <img class="invert dark:invert-0 w-5 h-5" src="../../assets/icons/comment.png" alt="Commentaire">
                        <span>${tweet.comments}</span>
                        </button>
                        <button class="flex items-center">
                        <img class="invert dark:invert-0 w-5 h-5" src="../../assets/icons/repost.png" alt="Repost">
                        <span>${tweet.reposts}</span>
                        </button>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <hr class="border-t my-4">
        `;
    }

    async checkMention(mention) {
        const formData = new FormData();
        formData.append('action', 'checkMention');
        formData.append('mention', mention);
        try {
            const response = await fetch("../../src/Controllers/HomeController.php", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            const responseData = await response.json();
            return responseData;

        } catch (error) {
            console.error("Erreur lors de la verifaction de la menttion:", error);
            alert("Une erreur est survenue lors de la vérification de la mention");
        }
    }

    async getPost(formData) {
        try {
            const response = await fetch("../../src/Controllers/HomeController.php", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new TypeError("La réponse n'est pas au format JSON!");
            }

            const responseData = await response.json();
            if (responseData.success) {
                return responseData;
            } else {
                console.error("Erreur serveur:", responseData.message);
                alert(responseData.message || "Une erreur est survenue");
            }
        } catch (error) {
            console.error("Erreur lors du chargement des posts:", error);
            alert("Une erreur est survenue lors du chargement des posts");
        }
    }

    calculateRelativeTime(dateString) {
        const postDate = new Date(dateString);
        const now = new Date();
        const diffSeconds = Math.floor((now - postDate) / 1000);

        if (diffSeconds < 60) return 'à l\'instant';
        if (diffSeconds < 3600) return `${Math.floor(diffSeconds / 60)}m`;
        if (diffSeconds < 86400) return `${Math.floor(diffSeconds / 3600)}h`;
        if (diffSeconds < 604800) return `${Math.floor(diffSeconds / 86400)}j`;
        return `${Math.floor(diffSeconds / 604800)}sem`;
    }

    async insertTweetInContainers(tweet) {
        const tweetElement = await this.createTweetElement(tweet);
        if (this.desktopTweetsContainer) {
            this.desktopTweetsContainer.insertAdjacentHTML('beforeend', tweetElement);
        }
        if (this.mobileTweetsContainer) {
            this.mobileTweetsContainer.insertAdjacentHTML('beforeend', tweetElement);
        }
    }

    async loadTweets() {
        if (this.isLoading) return;
        this.isLoading = true;
        this.loadingElement.classList.remove('hidden');

        try {
            const formData = new FormData();
            formData.append('action', 'getAllPosts');

            const response = await this.getPost(formData);
            if (response.success && response.posts) {
                for (const post of response.posts) {
                    const tweet = {
                        username: post.username || "User",
                        handle: post.display_name?.toLowerCase() || "user",
                        date: this.calculateRelativeTime(post.created_at) || "now",
                        content: post.content,
                        comments: post.comments_count || 0,
                        reposts: post.reposts_count || 0,
                        image_url: post.media?.[0]?.file_name || null,
                        user_id: post.user_id
                    };
                    await this.insertTweetInContainers(tweet);
                }
            } else if (response.message === "Pas de tweet") {
                await this.insertTweetInContainers({ message: response.message });
            }
        } catch (error) {
            console.error('Erreur lors du chargement des tweets:', error);
            this.loadingElement.textContent = 'Erreur lors du chargement';
        } finally {
            this.isLoading = false;
            this.loadingElement.classList.add('hidden');
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const tweetFeed = new TweetFeed();
    const tweetPostHandler = new TweetPost();
    const textareaDesktop = document.getElementById("post-text-area-desktop");
    const textareaMobile = document.getElementById("post-text-area-mobile");
    const userListDivDesktop = document.getElementById("user-desktop");
    const userListDivMobile = document.getElementById("user-mobile");
    const autoComplete = new handleAutoCompletion(textareaDesktop, textareaMobile, userListDivDesktop, userListDivMobile, "../../src/Controllers/HomeController.php", "@");
    autoComplete.init();
});
