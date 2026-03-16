const uniprice=1000;
const days=30;

const quantityInput= document.getElementById("quantity");
const totalPriceInput=document.getElementById("totalPrice");

function calculation()
{
    let quantity=parseInt(quantityInput.valur)||0;

    if (quantity < 0){
        alert("Quantity cant not be negetive! Resetting to 0.");
        quantity=0;
        quantityInput.value=0;
    }

    let total=uniprice*quantity*days;
    totalPriceInput.value=total;

    if(total>1000)
    {
        alert("Congratulatins! You are eligible for a gift coupon.");
    }

    quantityInput.addEventListener("input",CalculateTotal);
}
