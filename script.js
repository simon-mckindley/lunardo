
var discounted;

// Array to hold subtotal data for seats
var subPrices = {
    "STA": 0,
    "STP": 0,
    "STC": 0,
    "FCA": 0,
    "FCP": 0,
    "FCC": 0
};

// Calculates the total and sub prices from the inputs entered, then displays it in the relevant spans.
function updatePrice() {
    if (document.querySelector('input[name="day"]:checked') == null) return;

    const submitButton = document.getElementById("submit-butt");
    const priceElem = document.getElementById("price-div");
    let price = 0;

    // ** A4 ** added code to update subPrices array and related span elements.
    if (discounted) {
        for (let i in seatPrices.discounted) {
            subPrices[i] = seatPrices.discounted[i] * Number(document.getElementById(i).value);
            price += subPrices[i];
            document.getElementById(i + "-sub").innerHTML = `\$${subPrices[i].toFixed(2)}`;
        }
    } else {
        for (let i in seatPrices.fullprice) {
            subPrices[i] = seatPrices.fullprice[i] * Number(document.getElementById(i).value);
            price += subPrices[i];
            document.getElementById(i + "-sub").innerHTML = `\$${subPrices[i].toFixed(2)}`;
        }
    }

    document.getElementById("price").innerHTML = `\$${price.toFixed(2)}`;

    // ** A4 ** Sets the display value of the subtotal spans
    for (let a in subPrices) {
        let currentSpan = document.getElementById(a + "-sub");
        if (subPrices[a] == 0) {
            currentSpan.style.display = "none";
        } else {
            currentSpan.style.display = "block";
        }
    }

    if (price == 0) {
        priceElem.style.display = "none";
        submitButton.setAttribute("disabled", true);
        submitButton.style.backgroundColor = "grey";
    } else {
        priceElem.style.display = "flex";
        submitButton.removeAttribute("disabled");
        submitButton.style.backgroundColor = "rgb(50, 97, 184)";
    }

    return price;
}

// Corrects input entered to integers between 1 and 10 
function verifyInput(id) {
    let maxQty = 10;
    let qtyField = document.getElementById(id);
    let qtyValue = parseInt(qtyField.value);

    if (isNaN(qtyValue) || (qtyValue <= 0)) {
        qtyField.value = "";
    } else {
        qtyField.value = (qtyValue > maxQty) ? maxQty : qtyValue;
    }

    updatePrice();
    return qtyValue;
}

// ** A4 ** Increments value in number field and verifies value
function incrInput(id) {
    let currInput = document.getElementById(id);
    let currValue = parseInt(currInput.value);

    currInput.value = (isNaN(currValue)) ? 1 : (currValue + 1);
    verifyInput(id);
}

// ** A4 ** Decrements value in number field and verifies value
function decrInput(id) {
    let currInput = document.getElementById(id);
    let currValue = parseInt(currInput.value);

    currInput.value = (isNaN(currValue)) ? 0 : (currValue - 1);
    verifyInput(id);
}

// Sets the discounted variable depending on the session selected
function isDiscounted(time) {
    let day = document.querySelector('input[name="day"]:checked').value;

    switch (day) {
        case "MON":
            discounted = true;
            break;
        case "TUE":
        case "WED":
        case "THU":
        case "FRI":
            discounted = (time == "12pm") ? true : false;
            break;
        default:
            discounted = false;
    }

    updatePrice();
    return discounted;
}

// ** A4 ** Sets or unsets the users data in local storage depending on the checkbox value
function setLocalStorage() {
    if (typeof (Storage) !== 'undefined') {
        if (document.getElementById('remember-me').checked == true) {
            localStorage.setItem('name', document.getElementById('user[name]').value);
            localStorage.setItem('email', document.getElementById('user[email]').value);
            localStorage.setItem('mobile', document.getElementById('user[mobile]').value);
            localStorage.setItem('checked', 'true');
            document.getElementById('remember-label').innerHTML = "Forget Me";
        } else {
            localStorage.removeItem('name');
            localStorage.removeItem('email');
            localStorage.removeItem('mobile');
            localStorage.removeItem('checked');
            document.getElementById('remember-label').innerHTML = "Remember Me";
        }
    }
}

// ** A4 ** Sets the value of the user fields and Remember Me checkbox depending on local storage value
function checkStorage() {
    if (localStorage.checked == 'true') {
        document.getElementById('remember-me').checked = true;
        document.getElementById('remember-label').innerHTML = "Forget Me";
        document.getElementById('user[name]').value = localStorage.name;
        document.getElementById('user[email]').value = localStorage.email;
        document.getElementById('user[mobile]').value = localStorage.mobile;
    } else {
        document.getElementById('remember-me').checked = false;
        document.getElementById('remember-label').innerHTML = "Remember Me";
    }
}

// Sets the class of the link to the current section in the window to "current" so it can be styled
function setCurrentLink() {
    let navlinks = document.getElementsByTagName("nav")[0].getElementsByTagName("a");
    let sections = document.getElementsByTagName("main")[0].getElementsByTagName("section");

    for (let i = 0; i < sections.length; i++) {
        let secTop = sections[i].offsetTop - 2;
        let secBot = sections[i].offsetTop + sections[i].offsetHeight - 2;

        if ((window.scrollY >= secTop) && (window.scrollY < secBot)) {
            navlinks[i].classList.add("current");
        } else {
            navlinks[i].classList.remove("current");
        }
    }
}
