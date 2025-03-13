const openModal = () => {
  const body = document.querySelector("body");
  const modal = document.getElementById("edit-profile-modal");
  body.classList.add("overflow-hidden");
  modal.classList.remove("hidden");
};

const closeModal = () => {
  const body = document.querySelector("body");
  const modal = document.getElementById("edit-profile-modal");
  const form = document.getElementById("user-edit-profile");
  form.reset();
  modal.classList.add("hidden");
  body.classList.remove("overflow-hidden");
};

const selectTheme = (theme) => {
  document.querySelectorAll('input[name="theme"]').forEach((input) => {
    input.checked = input.value === theme;
  });
  const header = document.querySelector("html");
  header.classList.remove("light", "dark");
  header.classList.add(theme);

  updateUserTheme(theme);
};

const userFormData = async () => {
  const editProfileForm = document.getElementById("user-edit-profile");
  const formData = new FormData(editProfileForm);

  const changedFields = {};

  formData.forEach((value, key) => {
    if (value.trim() !== "") {
      changedFields[key] = value;
    }
  });

  if (changedFields.newPassword && !changedFields.oldPassword) {
    alert("Veuillez fournir votre ancien mot de passe pour pouvoir changé.");
    return false;
  }

  if (changedFields.newPassword && changedFields.oldPassword) {
    const isPasswordValid = await checkPasswordValidity(
      changedFields.oldPassword
    );

    if (!isPasswordValid) {
      return false;
    }
  }

  if (Object.keys(changedFields).length < 1) {
    return false;
  }

  const savedData = new FormData();
  savedData.append("action", "userEditProfile");
  savedData.append("data", JSON.stringify(changedFields));

  return savedData;
};

const saveChanges = async () => {
  const formData = await userFormData();
  if (formData) {
    try {
      fetch("../../src/Controllers/UserController.php", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            window.location.reload();
          } else {
            alert(data.message);
          }
        });
    } catch (error) {
      console.error("Error : ", error);
    }
  }
};

const updateUserTheme = (theme) => {
  const formData = new FormData();
  formData.append("action", "updateUserTheme");
  formData.append("data", theme);

  try {
    fetch("../../src/Controllers/UserController.php", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status !== "success") {
          alert(data.message);
        }
      });
  } catch (error) {
    console.error("Error : ", error);
  }
};

document.addEventListener("keydown", (event) => {
  const modal = document.getElementById("edit-profile-modal");
  if (event.key === "Escape" && modal) {
    closeModal();
  }
});

const modal = document.getElementById("edit-profile-modal");
modal.addEventListener("click", (event) => {
  if (event.target === modal) {
    closeModal();
  }
});

const checkPasswordValidity = async (passwordValidity) => {
  try {
    const response = await fetch("../../src/Controllers/AuthController.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ data: passwordValidity }),
    });

    const data = await response.json();

    if (data.status === "success") {
      return true;
    } else {
      alert(data.message);
      return false;
    }
  } catch (error) {
    console.error("Error : ", error);
    return false;
  }
};
