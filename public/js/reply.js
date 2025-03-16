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
  const imagesHtml = reply.images && reply.images.length > 0 
    ? `
    <div class="mt-3 mb-3 grid gap-2 p-2.5 rounded-[30px] mr-5 ${reply.images.length > 1 ? 'grid-cols-2' : 'grid-cols-1'}">
      ${reply.images.map(img => `
        <a href="${img}" class="flex justify-center items-center overflow-hidden" target="_blank">
          <img src="${img}" alt="Reply media" class="max-w-full object-contain rounded-lg">
        </a>
      `).join('')}
    </div>
  ` : '';

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
            ${imagesHtml}
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
  const desktopMediaInput = document.getElementById("file-input-desktop");
  const mobileMediaInput = document.getElementById("file-input-mobile");
  const hasMediaFiles = (desktopMediaInput?.files.length > 0) || (mobileMediaInput?.files.length > 0);
  
  postBtn.disabled = !hasContent && !hasMediaFiles;
  postBtnMobile.disabled = !hasContent && !hasMediaFiles;
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
      const mobileTextArea = document.getElementById("post-text-area-mobile");
      tweetContent.value = "";
      mobileTextArea.value = "";

      const desktopFileInput = document.getElementById("file-input-desktop");
      const mobileFileInput = document.getElementById("file-input-mobile");
      if (desktopFileInput) desktopFileInput.value = "";
      if (mobileFileInput) mobileFileInput.value = "";

      const desktopPreviewContainer = document.querySelector(".image-preview-container");
      const mobilePreviewContainer = document.querySelector("#mobile-modal .image-preview-container");
      if (desktopPreviewContainer) desktopPreviewContainer.remove();
      if (mobilePreviewContainer) mobilePreviewContainer.remove();

      closeMobileModal();
      updatePostBtn();
    } else {
      console.error("Erreur serveur:", responseData.message);
      alert(responseData.message || "Une erreur est survenue");
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi du tweet:", error);
    alert("Une erreur est survenue lors de l'envoi du tweet");
  }
};

const handleMediaSelection = (event, view) => {
  const files = Array.from(event.target.files);

  if (files.length > 4) {
    alert("Vous ne pouvez pas télécharger plus de 4 images");
    event.target.value = "";
    return;
  }

  if (!files.every((file) => file.type.startsWith("image/"))) {
    alert("Veuillez sélectionner uniquement des images");
    event.target.value = "";
    return;
  }

  const existingImageContainer = document.querySelector(
    view === "mobile"
      ? "#mobile-modal .image-preview-container"
      : ".image-preview-container"
  );
  if (existingImageContainer) {
    existingImageContainer.remove();
  }

  const imageContainer = document.createElement("div");
  imageContainer.className = "image-preview-container w-full grid gap-2";
  imageContainer.className += files.length > 1 ? " grid-cols-2" : " grid-cols-1";

  const loadImages = files.map((file) => {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const previewWrapper = document.createElement("div");
        previewWrapper.className = `relative ${
          files.length > 1 ? "h-[150px]" : "h-[300px]"
        }`;

        const previewImage = document.createElement("img");
        previewImage.src = e.target.result;
        previewImage.className = "w-full h-full object-cover rounded-lg";

        const deleteButton = document.createElement("button");
        deleteButton.className =
          "absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center";
        deleteButton.innerHTML = "x";
        deleteButton.onclick = (e) => {
          e.preventDefault();
          previewWrapper.remove();
          if (!imageContainer.children.length) {
            imageContainer.remove();
          }
        };

        previewWrapper.appendChild(previewImage);
        previewWrapper.appendChild(deleteButton);
        imageContainer.appendChild(previewWrapper);
        resolve();
      };
      reader.readAsDataURL(file);
    });
  });

  Promise.all(loadImages).then(() => {
    if (view === "mobile") {
      const mobileContainer = document.querySelector("#mobile-modal .flex-1");
      if (mobileContainer) {
        const textareaContainer = mobileContainer.querySelector(".flex.gap-4");
        if (textareaContainer) {
          mobileContainer.insertBefore(
            imageContainer,
            textareaContainer.nextSibling
          );
        }
      }
    } else {
      const desktopContainer = postTextArea.closest(".flex-grow.flex.flex-col.gap-4");
      if (desktopContainer) {
        const insertBeforeElement = desktopContainer.querySelector(".flex.justify-start") ||
          desktopContainer.lastElementChild;
        desktopContainer.insertBefore(imageContainer, insertBeforeElement);
      }
    }
  });
};

const submitAnswer = (device) => {
  const tweetContentDesktop = document.getElementById("post-text-area-desktop");
  const tweetContentMobile = document.getElementById("post-text-area-mobile");
  const tweetContent = device === "desktop" ? tweetContentDesktop : tweetContentMobile;
  const fileInput = device === "desktop" ? document.getElementById("file-input-desktop") : document.getElementById("file-input-mobile");

  if (!isValidTweetLength(tweetContent.value)) {
    return alert("Votre message est trop long !");
  }

  const sourcePost = document.getElementById("source-post");
  const sourcePostId = sourcePost.dataset.postId;
  const formData = new FormData();

  formData.append("content", tweetContent.value);
  formData.append("postId", sourcePostId);
  
  const mediaFiles = Array.from(fileInput.files);
  if (mediaFiles.length) {
    mediaFiles.forEach((file) => {
      formData.append("images[]", file);
    });
    formData.append("action", "addReplyToPostMedia");
  } else {
    formData.append("action", "addReplyToPost");
  }

  submitTweet(formData, device);
};

document.getElementById("upload-button-desktop")?.addEventListener("click", () => {
  document.getElementById("file-input-desktop").click();
});

document.getElementById("upload-button-mobile")?.addEventListener("click", () => {
  document.getElementById("file-input-mobile").click();
});

document.getElementById("file-input-desktop")?.addEventListener("change", (event) => {
  handleMediaSelection(event, "desktop");
  updatePostBtn(); 
});

document.getElementById("file-input-mobile")?.addEventListener("change", (event) => {
  handleMediaSelection(event, "mobile");
  updatePostBtn();
});

document.addEventListener("DOMContentLoaded", () => {
  replyListener();
});
