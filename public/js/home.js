class TweetPost {
    constructor() {
        this.textarea = document.getElementById('postTextarea');
        this.postButton = document.getElementById('postButton');
        this.uploadButton = document.getElementById('uploadButton');
        this.fileInput = document.getElementById('fileInput');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.textarea.addEventListener('input', () => this.handleTextareaInput());
        this.uploadButton.addEventListener('click', () => this.handleUploadClick());
        this.fileInput.addEventListener('change', (event) => this.handleFileSelection(event));
    }

    handleTextareaInput() {
        this.postButton.disabled = this.textarea.value.trim() === '';
    }

    handleUploadClick() {
        this.fileInput.click();
    }

    handleFileSelection(event) {
        const selectedFile = event.target.files[0];
        if (selectedFile) {
            console.log('Fichier sélectionné :', selectedFile.name);
            // Vous pouvez ajouter ici le code pour gérer le fichier sélectionné
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const tweetPost = new TweetPost();
});

let page = 1;
let isLoading = false;
const tweetsContainer = document.getElementById('tweets-container');
const loadingElement = document.getElementById('loading');

function createTweetElement(tweet) {
    return `
        <div class="p-4 max-w-xl">
            <div class="flex gap-3">
                <div class="w-12 h-12">
                    <img src="../../assets/icons/profile.png" alt="profile" class="invert dark:invert-0 w-full h-full rounded-full">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">${tweet.username}</span>
                        <span class="text-tertiary-500">@${tweet.handle}</span>
                        <span class="text-xs">•</span>
                        <span class="">${tweet.date}</span>
                    </div>
                    <div class="ml-0 mt-3">
                        <div class="text-small text-xl">
                            ${tweet.content}
                        </div>
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

const mockTweets = [
    {
        username: "John Doe",
        handle: "johndoe",
        date: "2h",
        content: "Premier tweet de test ! #coding",
        comments: 5,
        reposts: 2
    },
    {
        username: "Jane Smith",
        handle: "janesmith",
        date: "5h",
        content: "Hello world! Comment allez-vous ?",
        comments: 8,
        reposts: 4
    },
    {
        username: "Dev Master",
        handle: "devmaster",
        date: "1j",
        content: "Le JavaScript c'est la vie !",
        comments: 12,
        reposts: 7
    }
];

async function loadMoreTweets() {
    if (isLoading) return;
    
    isLoading = true;
    loadingElement.classList.remove('hidden');

    try {
        await new Promise(resolve => setTimeout(resolve, 1000));

        if (page > 3) {
            loadingElement.textContent = 'Plus aucun tweet à afficher';
            return;
        }
        const duplicatedTweets = Array(3).fill(mockTweets).flat();
        
        duplicatedTweets.forEach(tweet => {
            tweetsContainer.insertAdjacentHTML('beforeend', createTweetElement(tweet));
        });

        page++;
    } catch (error) {
        console.error('Erreur lors du chargement des tweets:', error);
        loadingElement.textContent = 'Erreur lors du chargement';
    } finally {
        isLoading = false;
        loadingElement.classList.add('hidden');
    }
}

const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
        loadMoreTweets();
    }
}, {
    rootMargin: '100px'
});

observer.observe(loadingElement);

loadMoreTweets();