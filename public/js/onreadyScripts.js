var pickListDialog;

$(document).ready(function() {

    var table = $('#report').DataTable( {
            dom: 'Bfrtip',
            scrollX: true,
            responsive: true,
            buttons: [
             'excel'
            ],
            

        } )
        .columns.adjust();

    // Fetch City list by country at order page
    if( document.querySelector('#city') !== undefined && document.querySelector('#city') !== null ){
        $('#city').select2({
            selectionCssClass: ':all:',
        });

        document.querySelector('#shipping_country').addEventListener('change', e=>{
            let shipping_country = document.querySelector('#shipping_country option:checked').value;
            let _token = document.querySelector('input[name="_token"]').value;
            
            let data = new FormData();
            data.append('shipping_country', shipping_country);
            data.append('_token', _token);
            fetch('orders/get_cities', {
                method: 'POST',
                body: data

            }).then(res=>res.json()).then(result=>{
                document.querySelector('#city').innerHTML = result;
            }).catch(e=>console.log(e));
        });

    } // if close

    // hide fulfillment popup at wms process order page
    if(document.querySelector('#boxOverlay') !== undefined && document.querySelector('#boxOverlay') !== null)
    {
        document.querySelector('#close-btn').addEventListener('click', e=>{
            if(!document.querySelector('#boxOverlay').classList.contains('hidden'))
            {
                document.querySelector('#boxOverlay').classList.add('hidden');
            }
        });

        document.querySelector('#btnCancel').addEventListener('click', e=>{
            if(!document.querySelector('#boxOverlay').classList.contains('hidden'))
            {
                document.querySelector('#boxOverlay').classList.add('hidden');
            }
        });
    } // if close

    //trigger fulfillment function
    if( document.querySelector('#btnFulfillment') !== undefined && document.querySelector('#btnFulfillment') !== null )
    {
        document.querySelector('#btnFulfillment').addEventListener('click', e=>{
            let selectedOrders = document.querySelectorAll('.tblOrders tbody input[type=checkbox]:checked');

            if(selectedOrders.length !== 0)
            {
                document.querySelector('#fulfillmentForm').reset();
                document.querySelector('#boxOverlay').classList.remove('hidden');
            } else {
                // alert('You must check at least one shipping order');
                alertify
                    .alert("You must check at least one shipping order.");
            }
        });
    } // if close

    // submit fulfillment form
    if( document.querySelector('#fulfillmentForm') !== undefined && document.querySelector('#fulfillmentForm') !== null)
    {
        document.querySelector('#fulfillmentForm').addEventListener('submit', e=>{
            e.preventDefault();
            let Orders = document.querySelectorAll('.tblOrders tbody input[type=checkbox]:checked');
            let selectedOrders = [];
            Orders.forEach(item=>{
                selectedOrders.push(item.value);
            });
            let form = new FormData(e.target);
            let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        
            form.append('selectedOrders', JSON.stringify(selectedOrders));
            form.append('_token', token);
            fetch('/ab-ajax/fulfillment',{
                method: 'POST',
                body:   form
            }).then(res=>res.json()).then(result=>{
                if(!document.querySelector('#boxOverlay').classList.contains('hidden'))
                {
                    document.querySelector('#boxOverlay').classList.add('hidden');
                }
                alertify.alert(result.msg);
                

            }).catch(e=>{
                if(!document.querySelector('#boxOverlay').classList.contains('hidden'))
                {
                    document.querySelector('#boxOverlay').classList.add('hidden');
                }
                console.log(e)
            });
            
        });
    }// ends

    // trigger pick n pack popup
    if(document.querySelector('#btnPickNPack') !== undefined && document.querySelector('#btnPickNPack') !== null)
    {
        document.querySelector('#btnPickNPack').addEventListener('click', e=>{
            let html = `
                <div id="PickNPack">
                    <div class="">
                        <div class="text-2xl"><h1 class="text-center font-bold">Pick N Pack</h1></div>
                    </div>

                    <div class="mt-3 mb-3">
                        <div class="text-xl"><h1 class="font-bold">Pending Work:</h1></div>
                    </div>

                    <div id="picknPackOrders"></div>

                </div>
            `;

            alertify.genericDialog || alertify.dialog('genericDialog',function(){
                return {
                    main:function(content){
                        this.setContent(content);
                    },
                    setup:function(){
                        return {
                            focus:{
                                element:function(){
                                    return this.elements.body.querySelector(this.get('selector'));
                                },
                                select:true
                            },
                            options:{
                                basic:true,
                                maximizable:false,
                                resizable:false,
                                padding:true, 
                                closableByDimmer: false,
                                onshow: fetchAssignedOrders,
                            }
                        };
                    },
                    settings:{
                        selector:undefined
                    }
                };
            });
            alertify.genericDialog (html);
            
            
        });
    }

    // assign tray event listener
    if(document.querySelector('#frmAssignTray') !== null && document.querySelector('#frmAssignTray') !== undefined)
    {
        document.querySelector('#frmAssignTray').addEventListener('submit', e=>{
            e.preventDefault();
            let form = new FormData(document.querySelector('#frmAssignTray'));
            let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
            form.append('_token', token);

            for(let pair in form.entries())
            {
                console.log(pair);
            }


        });
    } // end assign tray

    // trigger pick n pack function

    if( document.querySelector('#btnStartPicking') !== undefined && document.querySelector('#btnStartPicking') !== null )
    {
        document.querySelector('#btnStartPicking').addEventListener('click', e=>{
            let form = new FormData();
            let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
            form.append('_token', token);

            fetch('/ab-ajax/pickNpackInit',{
                method: 'POST',
                body: form,
            }).then(res=>res.json()).then(result=>{
                if(result.type == 'error')
                {
                    alertify.alert(result.msg);
                }
                else {
                    console.log(result);
                    let html = `
                    <form id="frmPickProducts" onsubmit="return false;">

                    `;

                    result.forEach((item, index)=>{
                        html += `
                            <div class="tab">
                                <div class="relative block w-full">
                                    <p class="text-center">${index + 1} of ${result.length}</p>
                                </div>

                                <div class="relative block w-full">
                                    <p class=""><strong class="text-yellow-500">Qty:</strong><span class="mx-3 text-xl">${item.order_qty}</span> picks from bin </p>
                                </div>

                                <div class="relative flex flex-col md:flex-row items-center">
                                    <div class="w-full">
                                        <img src="${item.image_path}" alt="productLabel"/>
                                    </div>
                                    <div class="md:ml-4 w-full">
                                        <h2 class="text-yellow-500 text-xl">${item.location}</h2>
                                        <h4 class="text-yellow-500 text-md">${item.bin_location}</h4>
                                        <p><span class="uppercase">Pick: </span> ${item.order_qty} for ${item.order_position}</p>
                                    </div>
                                </div>

                                <div class="relative mt-4">
                                    <p><strong class="text-lg">Label: </strong> ${item.label}</p>
                                    <p><strong class="text-lg">SKU: </strong> ${item.sku}</p>
                                    <p><strong class="text-lg">Position: </strong> ${item.order_position}</p>
                                    
                                    <input type="hidden" name="item[${index}][order_ass_user_id]" id="order_ass_user_id${index}" value="${item.order_ass_user_id}" />
                                    <input type="hidden" name="item[${index}][location_id]" id="location_id${index}" value="${item.location_id}" />
                                    <input type="hidden" name="item[${index}][bin_id]" id="bin_id${index}" value="${item.id}" />
                                    <input type="hidden" name="item[${index}][product_id]" id="product_id${index}" value="${item.product_id}" />
                                    <input type="hidden" name="item[${index}][qty_picked]" id="qty_picked${index}" value="${item.order_qty}" />


                                    <input class="mt-4 w-full border-gray-300 border-t-0 border-l-0 border-r-0 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" type="text" id="binLocation${index}" placeholder="Bin" onChange="validate('${item.bin_location}', this)"/>
                                    <input class="mt-4 w-full border-gray-300 border-t-0 border-l-0 border-r-0 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" type="text" id="productSku${index}" placeholder="SKU" onChange="validate('${item.sku}', this)"/>
                                    <input class="mt-4 w-full border-gray-300 border-t-0 border-l-0 border-r-0 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" type="text" id="orderPosition${index}" placeholder="Tray #" onChange="validate('${item.order_position}', this)"/>
                                    
                                </div>

                                <button id="pickListNext" class="mt-8 text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg w-full" onclick="pickItem(${index})">Pick</button>
                            </div>
                        `;
                    });
                    html += `
                    </form>`;
                    let options = {
                        basic:true,
                        maximizable:false,
                        resizable:false,
                        padding:true, 
                        closableByDimmer: false,
                    };
                    pickListDialog = makeDialog(html, options);
                    showTab(0);
                }
            }).catch(e=>console.log(e)); 


        });
    }

} ); // onready function

const fetchAssignedOrders = ()=>{
    let form = new FormData();
    let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    form.append('_token', token);

    fetch('/ab-ajax/fetchUserAssignedOrders',{
        method: 'POST',
        body: form,
    }).then(res=>res.json()).then(result=>{
        console.log(result);
    }).catch(e=>console.log(e));

};

const assignTray = id=>{
    let html = `
        <form id="frmAssignTray">
            <div class="relative mb-4 flex flex-col">
                <input type="hidden" id="order_assigned_users_id" name="order_assigned_users_id" value="${id}" />
                <label for="tray" class="leading-7 text-sm text-gray-600">Enter Tray</label>
                <input type="text" id="tray" name="tray" class="w-full bg-white rounded border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" required >
                <input type="submit" id="AssignTraybtn" value="Submit" class="mt-3 text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg"/>
            </div>
        </form>
    
    `;
    let options = {
        basic:true,
        maximizable:false,
        resizable:false,
        padding:true, 
        closableByDimmer: false,
    };
    let trayAssignDialog = makeDialog(html, options, '#tray');
    
    document.querySelector('#frmAssignTray').addEventListener('submit', e=>{
        e.preventDefault();
        let form = new FormData(document.querySelector('#frmAssignTray'));
        let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        form.append('_token', token);
        fetch('/ab-ajax/AssignTray',{
            method: 'POST',
            body:   form,
        }).then(res=>res.json()).then(result=>{
            if(result.type == 'refresh')
            {
                window.location.reload();
            } 
            else if(result.type == 'error') {
                alertify.alert(result.msg);
            }else 
            {
                console.log(result);
            }
        }).catch(e=>console.log(e));


        trayAssignDialog.close();
    });

    document.querySelector('#tray').addEventListener('keyup', e=>{
        if(e.target.value.length == 6)
        {
            document.querySelector('#AssignTraybtn').click();
        }
    });
};

const makeDialog = (html, options={}, selector='')=> {
    alertify.genericDialog || alertify.dialog('genericDialog',function(){
        return {
            main:function(content){
                this.setContent(content);
            },
            setup:function(){
                return {
                    focus:{
                        element:function(){
                            return this.elements.body.querySelector(this.get('selector'));
                        },
                        select:true
                    },
                    options:options
                };
            },
            settings:{
                selector:undefined
            }
        };
    });
    if(selector !== '')
    {
        return alertify.genericDialog (html).set('selector', selector);
    }
    else {
        return alertify.genericDialog (html);
    }
};

const UpdateTray = (tray, id) =>{
    console.log(tray, id );
}; 

const showTab = index =>{
    let tabs = document.querySelectorAll('.tab');
    tabs[index].style.display = 'block';
}; 

const pickItem = (index)=> {
    let bin = document.querySelector('#binLocation'+index);
    let sku = document.querySelector('#productSku'+index);
    let tray = document.querySelector('#orderPosition'+index);

    if(bin.value !== '' && sku.value !== '' && tray.value !== '')
    {
        let tabs = document.querySelectorAll('.tab');

        if(index < tabs.length - 1 )
            {
                tabs.forEach(item=>{
                    item.style.display = 'none';
                });
                showTab(index+1);
            } else {
                let form = new FormData(document.querySelector('#frmPickProducts'));
                let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
                form.append('_token', token);
                form.append('status', 'picked');
                
                fetch('/ab-ajax/addPickList', {
                    method: 'POST',
                    body: form
                })
                .then(res=>res.json())
                .then(result=>{
                    if(result.type == 'success')
                    {
                        document.location.reload();
                    } else {
                        alertify.alert(result.msg);
                    }
                }).catch(e=>console.log(e));
                pickListDialog.close();
            }
        

        
    } else {
        alertify.alert('Please fill all fields..');
    }
    

};

const validate = (dbValue, ele) => {
    if(ele.value !== dbValue)
    {
        alertify.alert('please enter correct value');
        ele.value = '';
    }
    
};

const shipOrder = orderID=>{
    let html = `
        <div id="ShipOrderPopup">
            <div class="header"><h1 class="text-center text-2xl uppercase font-bold text-yellow-500">Ship Order</h1></div>
            <div class="flex flex-col">
                <div class="relative mt-8 border-yellow-500 p-4 border-2 w-full" >
                    <p class="text-sm"><strong>Order Number:</strong> 18150</p>
                    <p class="text-sm"><strong>Customer Name:</strong> Azim Baig</p>
                    <p class="text-sm"><strong>Customer Contact:</strong> +973 36387778</p>
                    <p class="text-sm"><strong>Customer Address:</strong> Flat No 1, Building 2201, Road 1144, Block 711, Tubli, Manama, Bahrain</p>
                </div>
                <div class="relative mt-8 border-yellow-500 p-4 border-2 w-full" >
                    <p class="text-sm"><strong>Payment Method:</strong> Cash On Delivery</p>
                </div>
                <div class="relative mt-8 border-yellow-500 p-4 border-2 w-full" >
                    <p class="text-sm"><strong>Tracking Number:</strong> 1712424</p>
                </div>
            </div>
        </div>
    `;

    let options = {
        basic:true,
        maximizable:false,
        resizable:false,
        padding:true, 
        closableByDimmer: false,
        onshow: getShipOrderDetail(orderID),
    };
    let shipDialog = makeDialog(html, options);
};

const getShipOrderDetail = OrderID => {
    console.log(OrderID);
};