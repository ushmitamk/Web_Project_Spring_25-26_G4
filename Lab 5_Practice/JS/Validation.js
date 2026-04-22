const textInput=document.getElementById("textInput");
const resultDiv=document.getElementById("result");

function analyzeText(){
    let text=textInput.Value || "";
    let trimmedText=text.trim();

    if(trimmedText==="")
    {
        alert("Please enter some text!");
        textInput.Value="";
        resultInput.innerHTML="";
        return;
    }

    let charCount = text.length;
    let reversedText = text.split("").reverse().join("");
    let total = "Total Characters: " + charCount + "<br>" +
                "Total Words: " + wordCount + "<br>" +
                "Reversed Text: " + reversedText;

    resultInput.innerHTML = total;

    if(charCount > 100)
    {
        alert("You entered a long text!");
    }

    textInput.addEventListener("input", analyzeText);


}