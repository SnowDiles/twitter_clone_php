
class TweetFeed {
  constructor() {
    this.isLoading = false;
    this.desktopTweetsContainer = document.getElementById("tweets-container");
    this.mobileTweetsContainer = document.querySelector(".feed.md\\:invisible");
    this.loadingElement = document.getElementById("loading");
    this.userId = this.getUserIdFromURL();

    this.loadTweets();
  }

  getUserIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('userId');
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

    const tweetContentMentions = tweet.content.slice(tweet.content.search("@"));
    const mentions = tweetContentMentions.match(/@[a-zA-Z0-9_]+/g);
    if (mentions) {
      for (const mention of mentions) {
        const mentionResult = await this.checkMention(mention);
        if (mentionResult.success && mentionResult.userId) {
          const createLinkElement = document.createElement("a");
          createLinkElement.href = `./UserController.php?userId=${mentionResult.userId}`;
          createLinkElement.textContent = mention;
          createLinkElement.classList.add("text-primary-500");
          tweet.content = tweet.content.replace(
            mention,
            createLinkElement.outerHTML
          );
        }
      }
    }
    const tweetContentHashtag = tweet.content.slice(tweet.content.search("#"));
    const hashtags = tweetContentHashtag.match(/#[a-zA-Z0-9_]+/g);
    if (hashtags) {
      hashtags.forEach((hashtag) => {
        const createLinkElement = document.createElement("a");
        createLinkElement.href = `./SearchController.php?hashtag=${hashtag
          .trim()
          .substring(1)}`;
        createLinkElement.textContent = hashtag;
        createLinkElement.classList.add("text-primary-500");
        tweet.content = tweet.content.replace(
          hashtag,
          createLinkElement.outerHTML
        );
      });
    }

    let imagesHtml = "";
    if (tweet.image_url.length) {
      let imageElements = "";
      tweet.image_url.forEach((url) => {
        imageElements += `
                <a href="${url}" class="flex justify-center items-center overflow-hidden ${
          tweet.image_url.length > 1 ? "h-[150px]" : "h-[300px]"
        }" target="_blank">
                    <img src="${url}" alt="Tweet media" class="max-h-[300px] max-w-full object-contain rounded-lg">
                </a>
            `;
      });

      imagesHtml = `
            <div class="mt-3 mb-3 grid gap-2 ${
              tweet.image_url.length > 1 ? "grid-cols-2" : "grid-cols-1"
            }">
            ${imageElements}
            </div>
            `;
    }

    return `
        <div class="p-4 max-w-xl border-b border-gray-500">
            <div class="flex gap-3">
                <div class="w-13 h-13 flex-shrink-0">
                    <img src="../../assets/icons/profile.png" alt="profile" class="invert dark:invert-0 w-12 h-12 object-cover rounded-full">
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
                            ${imagesHtml}
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
        </div>
        `;
  }

  async checkMention(mention) {
    const formData = new FormData();
    formData.append("action", "checkMention");
    formData.append("mention", mention);
    try {
      const response = await fetch("../../src/Controllers/UserController.php", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });
      const responseData = await response.json();
      return responseData;
    } catch (error) {}
  }

  async getPost(formData) {
    try {
      const response = await fetch("../../src/Controllers/UserController.php", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
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

    if (diffSeconds < 60) return "à l'instant";
    if (diffSeconds < 3600) return `${Math.floor(diffSeconds / 60)}m`;
    if (diffSeconds < 86400) return `${Math.floor(diffSeconds / 3600)}h`;
    if (diffSeconds < 604800) return `${Math.floor(diffSeconds / 86400)}j`;
    return `${Math.floor(diffSeconds / 604800)}sem`;
  }

  async insertTweetInContainers(tweet) {
    const tweetElement = await this.createTweetElement(tweet);
    if (this.desktopTweetsContainer) {
      this.desktopTweetsContainer.insertAdjacentHTML("beforeend", tweetElement);
    }
    if (this.mobileTweetsContainer) {
      this.mobileTweetsContainer.insertAdjacentHTML("beforeend", tweetElement);
    }
  }

  getImageUrl(post) {
    if (!post.media) return [];
    return Object.values(post.media).map((media) => media.file_name);
  }

  async loadTweets() {
    if (this.isLoading) return;
    this.isLoading = true;
    this.loadingElement.classList.remove("hidden");

    try {
      const formData = new FormData();
      formData.append("action", "getAllPosts");
      if (this.userId) {
        formData.append('userId', this.userId);
      }

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
            image_url: this.getImageUrl(post),
            user_id: post.user_id,
          };
          await this.insertTweetInContainers(tweet);
        }
      } else if (response.message === "Pas de tweet") {
        await this.insertTweetInContainers({ message: response.message });
      }
    } catch (error) {
      console.error("Erreur lors du chargement des tweets:", error);
      this.loadingElement.textContent = "Erreur lors du chargement";
    } finally {
      this.isLoading = false;
      this.loadingElement.classList.add("hidden");
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const tweetFeed = new TweetFeed();
});
