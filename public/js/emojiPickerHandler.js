export function pickerVisibility(emojiToggle, idPickerContainer, textArea, emojiPickerItself,postButton) {
    const pickerContainer = document.getElementById(idPickerContainer);

    document.getElementById(emojiToggle).addEventListener('click', (event) => {
        if (pickerContainer) {
            pickerContainer.classList.toggle('hidden');
        }
        event.stopPropagation();
    });

    document.querySelectorAll('emoji-picker').forEach(picker => {
        picker.addEventListener('emoji-click', event => {
            const textAreaElement = document.getElementById(textArea);
            textAreaElement.value = textAreaElement.value + event.detail.unicode;
            document.getElementById(postButton).disabled = false;
        });
    });

    const htmlElement = document.querySelector('html');
    const pickerContainers = document.querySelectorAll(`#${emojiPickerItself}`);
    pickerContainers.forEach(pickerContainer => {
        if (htmlElement.classList.contains('dark')) {
            pickerContainer.classList.add('dark');
            pickerContainer.classList.remove('light');
        } else {
            pickerContainer.classList.add('light');
            pickerContainer.classList.remove('dark');
        }
    });

    document.addEventListener('click', (event) => {
        if (pickerContainer && !pickerContainer.contains(event.target) && !event.target.closest(`#${emojiToggle}`)) {
            pickerContainer.classList.add('hidden');
        }
    });
}