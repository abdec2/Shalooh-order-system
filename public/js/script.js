if(document.querySelector('#btnCreateLbl') !== null)
{
    document.querySelector('#btnCreateLbl').addEventListener('click', (e)=>{
        let loading = document.querySelector('.loading');
        document.querySelector('#orderStatus').value='in-transit';
        loading.style.display = 'block';
        let form = document.querySelector('#orderForm');

        let formData = new FormData(form);
    
        fetch('/create_label', {
            method: 'POST', 
            body: formData
        }).then(res=>res.blob()).then(blob=>{
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'download.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            form.submit();

        }).catch(() => alert('oh no!'));


        // form.action = '/create_label';
        // form.submit();
    });
}



