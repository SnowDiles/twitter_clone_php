export function pickerVisibility(emojiToggle, idPickerContainer,textArea,emojiPickerItself){

    document.getElementById(emojiToggle).addEventListener('click', () => {
        console.log(" je clique bien ");
        const pickerContainer = document.getElementById(idPickerContainer);
        if (pickerContainer) {
            pickerContainer.classList.toggle('hidden');
        }
    });

    document.querySelectorAll('emoji-picker').forEach(picker => {
        picker.addEventListener('emoji-click', event => {
            const textAreaElement = document.getElementById(textArea);
            textAreaElement.value = textAreaElement.value + event.detail.unicode;
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

}