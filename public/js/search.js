import { handleAutoCompletion } from "./autoCompletion.js";
class SearchFeed {
  constructor() {
    this.isLoading = false;

    this.searchBar = document.getElementById("search");
    this.desktopTweetsContainer = document.getElementById("tweets-container");
    this.mobileTweetsContainer = document.querySelector(".feed.md\\:hidden");
    this.loadingElement = document.getElementById("loading");

    this.autoCompleteContainer = document.getElementById("hashtag-desktop");
    this.initializeEventListeners();
  }
  initializeEventListeners() {
    this.searchBar.addEventListener("keypress", (event) => {
      if (event.key === "Enter") {
        this.submitSearch();
        this.autoCompleteContainer.style.display = "none";
      }
    });
  }
  submitSearch() {
    const textarea = this.searchBar;
    let searchContent = textarea.value.split("#")[1]?.split(" ")[0] || "";
    this.loadTweets(searchContent);
    this.clearSearchBar();
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

    let content = tweet.content;

    // Traitement des mentions
    const tweetContentMentions = content.slice(content.search("@"));
    const mentions = tweetContentMentions.match(/@[a-zA-Z0-9_]+/g);
    if (mentions) {
      for (const mention of mentions) {
        const mentionResult = await this.checkMention(mention);
        if (mentionResult.success && mentionResult.userId) {
          const createLinkElement = document.createElement("a");
          createLinkElement.href = `./UserController.php?userId=${mentionResult.userId}`;
          createLinkElement.textContent = mention;
          createLinkElement.classList.add("text-primary-500");
          content = content.replace(mention, createLinkElement.outerHTML);
        }
      }
    }

    // Traitement des hashtags
    const tweetContentHashtag = content.slice(content.search("#"));
    const hashtags = tweetContentHashtag.match(/#[a-zA-Z0-9_]+/g);
    if (hashtags) {
      for (const hashtag of hashtags) {
        const createLinkElement = document.createElement("a");
        createLinkElement.href = `./SearchController.php?hashtag=${hashtag
          .trim()
          .substring(1)}`;
        createLinkElement.textContent = hashtag;
        createLinkElement.classList.add("text-primary-500");
        content = content.replace(hashtag, createLinkElement.outerHTML);
      }
    }

    return `
        <div class="p-4 max-w-xl">
            <div class="flex gap-3">
                <div class="w-12 h-12">
                    <img src="../../assets/icons/profile.png" alt="profile" class="invert dark:invert-0 w-full h-full rounded-full">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <a class="text-xl" href="./UserController.php?userId=${
                          tweet.user_id
                        }">${tweet.username}</a>
                        <a class="text-tertiary-500" href="./UserController.php?userId=${
                          tweet.user_id
                        }">@${tweet.handle}</a>
                        <span class="text-xs">•</span>
                        <span class="">${tweet.date}</span>
                    </div>
                    <div class="ml-0 mt-3">
                        <div class="text-small text-xl">
                            ${content}
                        </div>
                        ${
                          tweet.image_url
                            ? `
                        <a href="${tweet.image_url}" class="mt-3 mb-3 block h-[300px] w-[300px] overflow-hidden" target="_blank">
                            <img src="${tweet.image_url}" alt="Tweet media" class="w-full h-full object-cover rounded-lg">
                        </a>
                        `
                            : ""
                        }
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

  async getPost(formData) {
    try {
      const response = await fetch(
        "../../src/Controllers/SearchController.php",
        {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
          body: formData,
        }
      );

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        throw new TypeError("La réponse n'est pas au format JSON!");
      }

      return await response.json();
    } catch (error) {
      console.error("Erreur lors de la récupération des posts:", error);
      return { success: false, posts: [] };
    }
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

  noPost() {
    return (this.desktopTweetsContainer.innerHTML = `
                <div class="p-4 max-w-xl text-center text-gray-500">
                    Aucun post trouvé.
                </div>
            `);
  }
  clearSearchBar() {
    this.searchBar.innerHTML = "";
  }
  getHashtagFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get("hashtag") || "";
  }

  async checkMention(mention) {
    const formData = new FormData();
    formData.append("action", "checkMention");
    formData.append("mention", mention);
    try {
      const response = await fetch(
        "../../src/Controllers/SearchController.php",
        {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
          body: formData,
        }
      );
      const responseData = await response.json();
      return responseData;
    } catch (error) {
      console.error("Erreur lors de la verifaction de la menttion:", error);
      alert("Une erreur est survenue lors de la vérification de la mention");    

    }
  }

  async loadTweets(hashtag) {
    this.searchBar.value = "#" + hashtag;
    if (this.isLoading) return;
    this.isLoading = true;
    this.loadingElement.classList.remove('hidden');
    try {
      this.desktopTweetsContainer.innerHTML = "";
      this.mobileTweetsContainer.innerHTML = "";

      const formData = new FormData();
      formData.append("hashtag", hashtag);
      formData.append("action", "getAllPosts");

      const response = await this.getPost(formData);
      if (response.success && response.posts.length > 0) {
        response.posts.forEach((post) => {
          const tweet = {

            username: post.username || "User",
            handle: post.display_name?.toLowerCase() || "user",
            date: this.calculateRelativeTime(post.post_created_at) || "now",
            content: post.content,
            comments: post.comments_count || 0,
            reposts: post.reposts_count || 0,
            image_url: post.media?.[0]?.file_name || null,
            user_id: post.user_id,
          };
          this.insertTweetInContainers(tweet);
        });
      } else {
        this.noPost();
      }
    } catch (error) {
      this.loadingElement.textContent = 'Erreur lors du chargement';

    } finally {
      this.isLoading = false;
      this.loadingElement.classList.add('hidden');
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const tweetFeed = new SearchFeed();
  const textareaDesktop = document.getElementById("search");
  const hashtagListDivDesktop = document.getElementById("hashtag-desktop");
  const autoComplete = new handleAutoCompletion(
    textareaDesktop,
    textareaDesktop,
    hashtagListDivDesktop,
    hashtagListDivDesktop,
    "../../src/Controllers/SearchController.php",
    "#"
  );
  autoComplete.init();

  const hashtag = tweetFeed.getHashtagFromURL();

  if (hashtag) {
    tweetFeed.loadTweets(hashtag);
  }
});
