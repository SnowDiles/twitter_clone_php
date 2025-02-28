export class handleAutoCompletion {
    constructor(textareaDesktop, textareaMobile, userListDivDesktop, userListDivMobile, fetchUrl) {
      this.textareaDesktop = textareaDesktop;
      this.textareaMobile = textareaMobile;
      this.userListDivDesktop = userListDivDesktop;
      this.userListDivMobile = userListDivMobile;
      this.fetchUrl = fetchUrl; 
    }
  
    async init() {
      // Initialize input listeners for both desktop and mobile textareas
      this.textareaDesktop?.addEventListener("input", () => this.handleInput(this.textareaDesktop, this.userListDivDesktop));
      this.textareaMobile?.addEventListener("input", () => this.handleInput(this.textareaMobile, this.userListDivMobile));
    }
  
    async handleInput(textarea, userListDiv) {
      const match = this.getUsernameMatch(textarea);
      if (match) {
        try {
          const users = await this.fetchUsers(match[1]);
          this.displayUserSuggestions(users, userListDiv);
        } catch (error) {
          this.hideUserList(userListDiv);
        }
      } else {
        this.hideUserList(userListDiv);
      }
    }
  
    getUsernameMatch(textarea) {
      return textarea.value.match(/@(\S+)$/);
    }
  
    async fetchUsers(username) {
      const formData = new FormData();
      formData.append("username", username);
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
        return data.data.user;
      } else {
        throw new Error("Error fetching users");
      }
    }
  
    displayUserSuggestions(users, userListDiv) {
      let userItems;
      if (users.length > 0) {
        userItems = users
          .map(
            (user) => `
                  <div class="user-item cursor-pointer p-2 hover:bg-gray-100" data-username="${user.username}">
                  @${user.username}
                  </div>
              `
          )
          .join("");
      } else {
        userItems = '<div class="p-2">Pas utilisateur trouvé</div>';
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
      const newValue = textarea.value.replace(/@\S+$/, `@${username} `);
      textarea.value = newValue;
      textarea.focus();
      textarea.setSelectionRange(newValue.length, newValue.length);
    }
  
    hideUserList(userListDiv) {
      userListDiv.style.display = "none";
    }
  }
  