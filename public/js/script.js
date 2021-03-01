if(document.querySelector('#btnCreateLbl') !== null)
{
    document.querySelector('#btnCreateLbl').addEventListener('click', (e)=>{

        let form = document.querySelector('#orderForm');
        let formData = new FormData(form);
        let packageSize = document.querySelector('#shipping_package_size').value;
        let package_length = document.querySelector('#package_length').value;
        let package_width = document.querySelector('#package_width').value;
        let package_height = document.querySelector('#package_height').value;
        
        if(packageSize !== '' && package_length !== '' && package_width !== '' && package_height !== ''){
            let loading = document.querySelector('.loading');
            document.querySelector('#orderStatus').value='in-transit';
            loading.style.display = 'block';

        
            fetch('/create_label', {
                method: 'POST', 
                body: formData
            }).then(res=>res.blob()).then(blob=>{
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = document.querySelector('#orderID').value+''+getCurrentDate()+'.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                form.submit();

            }).catch(() => alert('oh no!'));

            // fetch('/create_label', {
            //     method: 'POST', 
            //     body: formData
            // }).then(res=>res.json()).then(resu=>{
            //     console.log(result);
            //     loading.style.display = 'none';
            // }).catch(() => {
            //     alert('oh no!')
            //     loading.style.display = 'none';
            // });

           
        } else {
            alert('Please select package size.');
        }

        // form.action = '/create_label';
        // form.submit();
        
    });
}


const loadPackageDimension = (value)=>{
    if(value !== '' && value !== 'other')
    {
        let dimensions = value.split('X');
        document.querySelector('#package_length').value = dimensions[0];
        document.querySelector('#package_width').value = dimensions[1];
        document.querySelector('#package_height').value = dimensions[2];
        
        calculateVolWeight();
    } else 
    {
        document.querySelector('#package_length').value = '';
        document.querySelector('#package_width').value = '';
        document.querySelector('#package_height').value = '' 
    }
};

document.querySelector('#package_length').addEventListener('focusout', calculateVolWeight);
document.querySelector('#package_width').addEventListener('focusout', calculateVolWeight);
document.querySelector('#package_height').addEventListener('focusout', calculateVolWeight);



function calculateVolWeight()
{
    let length = document.querySelector('#package_length').value;
    let width = document.querySelector('#package_width').value;
    let height = document.querySelector('#package_height').value;

    if(length !== '' && width !== '' && height !== '')
    {
        let volWeight = (length * width * height) / 5000;

        volWeight = volWeight.toFixed(2);
        
        document.querySelector('#totalVolWeight').value = volWeight;

    }

} // function ends here

const getCurrentDate = ()=>{
    let date = new Date();
    let day = date.getDate();
    let month = date.getMonth()+1;
    let year = date.getFullYear();

    return day+''+month+''+year;
};

