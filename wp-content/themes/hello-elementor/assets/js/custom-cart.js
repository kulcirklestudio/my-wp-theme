var addBtn = document.querySelectorAll(".add-btn");
var cartDiv = document.querySelectorAll(".to-cart-btn");
var cartBtn = document.querySelectorAll(".add_to_cart_button");

cartDiv.forEach((cartBtn) => {
    cartBtn.style.display = "none";
})


cartBtn.forEach((cartbtn) => {
    // console.log(cartbtn);
    function handleCartClick() {
        const previousEle = cartbtn.previousElementSibling;
        if (previousEle) {
            console.log(previousEle);
        } else {
            console.log("Element Not");
        }
    }
    cartbtn.addEventListener("click", handleCartClick);
});


addBtn.forEach((addBtn) => {

    function handleClick() {

        const closestCard = addBtn.nextElementSibling;
        console.log(closestCard);

        if (closestCard) {
            closestCard.style.display = "block";
            addBtn.style.display = "none";
        } else {
            closestCard.style.display = "none";
            addBtn.style.display = "block";
        }
    }

    addBtn.addEventListener("click", handleClick);
});
// ------ end js---------
// todaydsfsdf