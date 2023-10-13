function preventTyping(event) {
    const keyPressed = event.key;

    if (!/^\d$/.test(keyPressed) && keyPressed !== "Backspace") {
    event.preventDefault();
    }
}

function handleInput(inputElement) {
    const maxQuantity = parseInt(inputElement.getAttribute("max"));
    let value = parseInt(inputElement.value);

    if (isNaN(value)) {
        value = 1;
    }

    if (value > maxQuantity) {
        inputElement.value = maxQuantity;
    }
}