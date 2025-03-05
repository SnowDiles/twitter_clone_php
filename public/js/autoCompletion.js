export class handleAutoCompletion {
  constructor(
    textareaDesktop,
    textareaMobile,
    userListDivDesktop,
    userListDivMobile,
    fetchUrl,
    symbol
  ) {
    this.textareaDesktop = textareaDesktop;
    this.textareaMobile = textareaMobile;
    this.userListDivDesktop = userListDivDesktop;
    this.userListDivMobile = userListDivMobile;
    this.fetchUrl = fetchUrl;
    this.symbol = symbol;
  }
  async init() {
    this.textareaDesktop?.addEventListener("input", () =>
      this.handleInput(this.textareaDesktop, this.userListDivDesktop)
    );
    this.textareaMobile?.addEventListener("input", () =>
      this.handleInput(this.textareaMobile, this.userListDivMobile)
    );
  }
  async handleInput(textarea, userListDiv) {
    const match = this.getUsernameMatch(textarea);
    if (match) {
      try {
        const datas = await this.fetchData(match[1]);
        this.displayUserSuggestions(datas, userListDiv);
      } catch (error) {
        this.hideUserList(userListDiv);
      }
    } else {
      this.hideUserList(userListDiv);
    }
  }
  getUsernameMatch(textarea) {
    const regex = new RegExp(`\\${this.symbol}(\\S+)$`);
    return textarea.value.match(regex);
  }
  async fetchData(datas) {
    const formData = new FormData();
    formData.append(this.symbol === "@" ? "username" : "hashtag", datas);
    formData.append("action", "autoCompletation");

    const response = await fetch(this.fetchUrl, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    if (data.success) {
      return data.data.user || data.data.hashtag;
    } else {
      throw new Error("Error fetching data");
    }
  }
  displayUserSuggestions(items, userListDiv) {
    let userItems;
    if (items.length > 0) {
      userItems = items
        .map(
          (item) => `
                    <div class="user-item cursor-pointer p-2 hover:bg-gray-100" data-username="${
                      this.symbol === "@" ? item.username : item.tag
                    }">
                        ${this.symbol}${
            this.symbol === "@" ? item.username : item.tag
          }
                    </div>
                `
        )
        .join("");
    } else {
      userItems = '<div class="p-2">Aucun résultat trouvé</div>';
    }
    userListDiv.innerHTML = userItems;
    userListDiv.style.display = "block";
    this.attachUserItemClickHandlers(userListDiv);
  }
  attachUserItemClickHandlers(userListDiv) {
    userListDiv.querySelectorAll(".user-item").forEach((item) => {
      item.addEventListener("click", () => {
        const username = item.getAttribute("data-username");
        this.replaceUsernameInTextarea(username, this.textareaDesktop);
        this.replaceUsernameInTextarea(username, this.textareaMobile);
        this.hideUserList(userListDiv);
      });
    });
  }
  replaceUsernameInTextarea(username, textarea) {
    const newValue = textarea.value.replace(
      new RegExp(`\\${this.symbol}\\S+$`),
      `${this.symbol}${username} `
    );
    textarea.value = newValue;
    textarea.focus();
    textarea.setSelectionRange(newValue.length, newValue.length);
  }
  hideUserList(userListDiv) {
    userListDiv.style.display = "none";
  }
}
