export function searchUser(userSearchTextArea) {
  userSearchTextArea.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      const value = userSearchTextArea.value;
      const firstMention = value
        .split(" ")
        .find((word) => word.startsWith("@"));
      if (firstMention) {
        const userName = firstMention.substring(1);
        const formData = new FormData();
        formData.append("action", "searchUser");
        formData.append("userName", userName);

        (async () => {
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

            const data = await response.json();
            if (data.success) {
                if(data.data.userId !== null){
                    window.location.href = `./UserController.php?userId=${data.data.userId}`;
                }

            } else {
              throw new Error("Error fetching data");
            }
          } catch (error) {
            console.error("Error:", error);
          }
        })();
      }
    }
  });
}
