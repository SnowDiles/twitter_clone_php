const postBtn = document.getElementById("post-button-desktop");
const postBtnMobile = document.getElementById("post-button-mobile");
const postTextArea = document.getElementById("post-text-area-desktop");
const mobileBtn = document.getElementById("btn-mobile-modale");
const closeBtn = document.getElementById("close-mobile-modal");

postBtn.addEventListener("click", () => submitAnswer("desktop"));
postTextArea.addEventListener("input", () => updatePostBtn());
mobileBtn.addEventListener("click", () => openMobileModal());
closeBtn.addEventListener("click", () => closeMobileModal());
postBtnMobile.addEventListener("click", () => submitAnswer("mobile"));

const openMobileModal = () => {
  const mobileModal = document.getElementById("mobile-modal");
  mobileModal.classList.remove("hidden");
  mobileModal.classList.add("flex");
};

const closeMobileModal = () => {
  const mobileModal = document.getElementById("mobile-modal");
  const mobileTextArea = document.getElementById("post-text-area-mobile");

  mobileTextArea.value = "";

  mobileModal.classList.remove("flex");
  mobileModal.classList.add("hidden");
};

const replyListener = () => {
  document.addEventListener("click", (event) => {
    if (event.target.closest(".repost-button")) {
      return;
    }

    const tweetContainer = event.target.closest(".tweet-container");
    if (tweetContainer) {
      const postId = tweetContainer.getAttribute("data-post-id");
      handleTweetClick(postId);
    }
  });
};

const handleTweetClick = (postId) => {
  if (postId) {
    window.location.href =
      "./HomeController.php?request=reply&postId=" + postId;
  }
};

const createReply = (reply) => {
  let imagesHtml = "";
  if (reply.images && reply.images.length) {
    imagesHtml = `
        <div class="mt-2 flex flex-wrap gap-2">
          ${reply.images
            .map(
              (img) =>
                `<img src="${img}" alt="Tweet image" class="max-w-full rounded-lg">`
            )
            .join("")}
        </div>
      `;
  }

  // Create the tweet element
  const tweetElement = document.createElement("div");
  tweetElement.className =
    "p-4 w-full border-b border-b-black dark:border-b-white tweet-container";
  tweetElement.dataset.postId = reply.postId;

  tweetElement.innerHTML = `
      <div class="flex gap-3">
        <div class="w-13 h-13 flex-shrink-0">
          <img src="../../assets/icons/profile.png" alt="profile" class="invert dark:invert-0 w-12 h-12 object-cover rounded-full">
        </div>
        <div>
          <div class="flex items-center gap-2">
            <a class="text-xl" href="./UserController.php?userId=${reply.userId}">${reply.userDisplayName}</a>
            <a class="text-tertiary-500" href="./UserController.php?userId=${reply.userId}">@${reply.userName}</a>
            <span class="text-xs">•</span>
            <span class="">${reply.createdAt}</span>
          </div>
          <div class="ml-0 mt-3">
            <div class="text-small text-xl break-all max-w-full">
              ${reply.content}
            </div>
            <div class="flex items-center gap-4 mt-2">
              <button class="flex items-center">
                <img class="invert dark:invert-0 w-5 h-5" src="../../assets/icons/comment.png" alt="Commentaire">
              </button>
              <button class="repost-button flex items-center" data-post-id="${reply.postId}">
                <img class="invert dark:invert-0 w-5 h-5" src="../../assets/icons/repost.png" alt="Repost">
                <span></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

  // Append the new tweet to the container
  const tweetsContainer = document.getElementById("tweets-container");
  if (tweetsContainer) {
    tweetsContainer.prepend(tweetElement);
  }
};

const updatePostBtn = () => {
  const hasContent = postTextArea.value.trim() !== "";
  postBtn.disabled = !hasContent;
};

const isValidTweetLength = (content) => {
  return content.length <= 140 && content.length >= 0;
};

const submitTweet = async (formData, view) => {
  try {
    const response = await fetch("../../src/Controllers/HomeController.php", {
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
      createReply(responseData.data);
      const tweetContent = document.getElementById("post-text-area-desktop");
      tweetContent.value = "";
      closeMobileModal();
    } else {
      console.error("Erreur serveur:", responseData.message);
      alert(responseData.message || "Une erreur est survenue");
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi du tweet:", error);
    alert("Une erreur est survenue lors de l'envoi du tweet");
  }
};

const submitAnswer = (device) => {
  if (!isValidTweetLength(postTextArea.value.trim())) {
    return;
  }

  const tweetContentDesktop = document.getElementById("post-text-area-desktop");
  const tweetContentMobile = document.getElementById("post-text-area-mobile");
  const tweetContent =
    device === "desktop" ? tweetContentDesktop : tweetContentMobile;

  const view = "desktop";
  if (!isValidTweetLength(tweetContent.value)) {
    return alert("Votre message est trop long !");
  }

  const sourcePost = document.getElementById("source-post");
  const sourcePostId = sourcePost.dataset.postId;
  const formData = new FormData();

  formData.append("content", tweetContent.value);
  formData.append("postId", sourcePostId);
  formData.append("action", "addReplyToPost");

  submitTweet(formData, view);
};

document.addEventListener("DOMContentLoaded", () => {
  replyListener();
});
