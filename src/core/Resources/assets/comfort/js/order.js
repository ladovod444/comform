var deleteProduct = document.getElementsByClassName('delete-product');
var count = document.getElementsByClassName('count');
var totalResult = document.getElementsByClassName('total-result');


getTotal();


for(let d = 0; d < deleteProduct.length; d++)
{
    let del = deleteProduct[d];
    del.onclick = function()
    {
            getTotal();
    }

}



function getTotal()
{


    let total = [];
    let currency = '';

    for(let i = 0; i < count.length; i++)
    {
        let plus = count[i].children[0];
        let minus = count[i].children[2];
        let result = count[i].children[1];


        result.onchange = function()
        {
            getTotal();
        }

        plus.onclick = function()
        {
            getTotal();
        }

        minus.onclick = function()
        {

            getTotal();
        }
    }






    for(let j = 0; j < count.length; j++)
    {
        let result = count[j].children[1];

        total.push(result.value * result.getAttribute('data-price'));
        currency = result.getAttribute('data-currency');

    }


    let sum = total.reduce(function(a, b)
    {
        return a + b;
    }, 0);

    for(let i = 0; i < totalResult.length; i++)
    {

        let currencyCode = currency === 'RUR' ? 'RUB' : currency;

        let USDollar = new Intl.NumberFormat('ru-Ru', {
            style: 'currency',
            currency: currencyCode,
            //useGrouping: false,
            maximumFractionDigits: 0,
        });
        totalResult[i].firstChild.innerHTML = USDollar.format(sum / 100)

    }

}


