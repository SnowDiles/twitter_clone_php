import { handleAutoCompletion } from "./autoCompletion.js";
document.addEventListener("DOMContentLoaded", () => {
  const textareaDesktop = document.getElementById("search");
  const hashtagListDivDesktop = document.getElementById("hashtag-desktop");
  const autoComplete = new handleAutoCompletion(textareaDesktop, textareaDesktop, hashtagListDivDesktop, hashtagListDivDesktop, "../../src/Controllers/SearchController.php","#");
  autoComplete.init();
});
