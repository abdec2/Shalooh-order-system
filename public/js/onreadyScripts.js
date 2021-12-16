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

    var table1 = $('#tblListProduct').DataTable({
        dom: 'Bfrtip',
        scrollX: true,
        responsive: true,
        buttons: [
         'excel'
        ],
        

    }).columns.adjust();

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

const shipOrder = async orderID=>{
    let form = new FormData();
    let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    form.append('_token', token);
    form.append('orderID', orderID);

    let response = await fetch('/ab-ajax/getShipOrderDetails', {method: 'POST', body: form});

    let result = await response.json();

    if(result.type == 'error')
    {
        alertify.alert(result.msg);
        return;
    }
    //${orderID}, '${result[0].order_number}', '${result[0].customer_name}', '${result[0].customer_contact}', '${result[0].shipping_address1}
    
    let html = `
        <div id="ShipOrderPopup">
            <div class="header mb-8"><h1 class="text-center text-2xl uppercase font-bold text-yellow-500">Ship Order</h1></div>
            <div class="flex flex-col">
                <div class="flex flex-row item-center justify-between">
                    <h1 class="font-bold text-lg">Customer Details:</h1>
                    <a id="btnCustDetEdit" class="leading-7 text-blue-300 cursor-pointer">edit</a>
                </div>
                <div id="divCustomerDetails" class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Order Number:</strong> ${result[0].order_number}</p>
                    <p class="text-sm"><strong>Customer Name:</strong> ${result[0].customer_name}</p>
                    <p class="text-sm"><strong>Customer Contact:</strong> ${result[0].customer_contact}</p>
                    <p class="text-sm"><strong>Shipping Address 1:</strong> ${result[0].shipping_address1}</p>
                    <p class="text-sm"><strong>Shipping Address 2:</strong> ${(result[0].shipping_address2 !== null) ? result[0].shipping_address2 : ''}</p>
                    <p class="text-sm"><strong>State / Province:</strong> ${(result[0].state !== null) ? result[0].state : ''}</p>
                    <p class="text-sm"><strong>Postal/Zip</strong> ${(result[0].postal !== null) ? result[0].postal : ''}</p>
                    <p class="text-sm"><strong>City:</strong> ${result[0].city}</p>
                    <p class="text-sm"><strong>Country:</strong> ${result[0].country}</p>
                </div>

                <div><h1 class="font-bold text-lg">Payment Details: </h1></div>
                <div class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Payment Method:</strong> ${result[0].payment_method}</p>
                </div>
                <div class="flex flex-row item-center justify-between">
                    <h1 class="font-bold text-lg">Shipment Details: </h1>
                    <a onclick="editShipmentDetail(${orderID}, '${result[0].shipping_carrier}', '${result[0].tracking_no}', '${result[0].total_weight}', '${result[0].total_vol_weight}', '${result[0].package_length}', '${result[0].package_width}', '${result[0].package_height}')" class="leading-7 text-blue-300 cursor-pointer">edit</a>
                </div>
                <div id="divShippingDetails" class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Shipping Carrier:</strong> ${result[0].shipping_carrier}</p>
                    <p class="text-sm"><strong>Tracking Number:</strong> ${result[0].tracking_no}</p>
                    <p class="text-sm"><strong>Total Weight:</strong> ${result[0].total_weight}KG</p>
                    <p class="text-sm"><strong>Total Volumetric Weight:</strong> ${result[0].total_vol_weight}Kg</p>
                    <p class="text-sm"><strong>Package Dimensions:</strong> L: ${result[0].package_length}cm X W: ${result[0].package_width}cm X H: ${result[0].package_height}cm</p>
                </div>
          
                <div><h1 class="font-bold text-lg">Ordered Products: </h1></div>
                <div class="relative mb-5 border-yellow-500 p-4 border-2 w-full overflow-x-auto rounded" >
                    <table class="table-auto w-full">
                    <tbody>
                        ${result.map(getproductHTMLForShipOrderPOPup).join('')}
                    </tbody>
                    </table>
                </div>
                <div class="relative w-full flex flex-col md:flex-row justify-around" >
                    <a href="https://www.shalooh.com/wp-admin/admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=${result[0].order_number}&order_key=${result[0].orderData1.order_key}" class="text-white mb-3 bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-md ">Get Invoice</a>
                    <button onclick="create_label(${orderID}, ${result[0].order_number})" class="text-white mb-3 bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-md">Create Label</button>
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
    };
    let shipDialog = makeDialog(html, options);

    document.querySelector('#btnCustDetEdit').addEventListener('click', e=>{
        editConstumerDetail(result, orderID);
    });
};


const getproductHTMLForShipOrderPOPup = (item, index) =>
{
    return `
        <tr>
            <td class="w-1/4 text-center"><img src="${item.image_path}" alt="" /></td>
            <td class="w-2/4 text-center"><strong>SKU:</strong> ${item.sku}</td>
            <td class="w-1/4 text-center"><strong>Qty:</strong> ${item.qty_picked}</td>
        </tr>
    `;
}
//orderID, orderNumber, Name, Contact, Address
const editConstumerDetail = async (obj, orderID) => {
   
    let resultCountry = await getCountries();

    let resultCity = await getCitiesByCountry(obj[0].country);

    let html = `
    <form id="frmCustDetail" onsubmit="return false;">
        <p class="text-sm"><strong>Order Number:</strong> ${obj[0].order_number}</p>
        <p class="text-sm"><strong>Customer Name:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="customerName" id="customerName" value="${obj[0].customer_name}" /></p>
        <p class="text-sm"><strong>Customer Contact:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="customerContact" id="customerContact" value="${obj[0].customer_contact}" /></p>
        <p class="text-sm"><strong>Shipping Address 1:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="shipping_address1" id="shipping_address1" value="${obj[0].shipping_address1}" /></p>
        <p class="text-sm"><strong>Shipping Address 2:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="shipping_address2" id="shipping_address2" value="${(obj[0].shipping_address2 !== null) ? obj[0].shipping_address2 : ''}" /></p>

        <p class="text-sm"><strong>State / Province:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="state" id="state" value="${(obj[0].state !== null) ? obj[0].state : ''}" /></p>

        <p class="text-sm"><strong>Postal / Zip:</strong> <input class="w-full border-gray-300 rounded focus:outline-none border" name="postal" id="postal" value="${(obj[0].postal !== null) ? obj[0].postal : ''}" /></p>

        <p class="text-sm"><strong>City:</strong> 

            <select id="city" name="city"
                class="w-full border-gray-300 rounded focus:outline-none border focus:ring-transparent focus:border-gray-300"
                required>
                <option value="" >Select</option>
                ${resultCity.map(item=>{
                    return `<option value="${item.cities}" ${(obj[0].city == item.cities) ? 'selected' : ''}>${item.cities} </option>`
                }).join('')}
            </select>
        
        </p>

        <p class="text-sm"><strong>Country:</strong> 

            <select id="country" name="country"
                class="w-full border-gray-300 rounded focus:outline-none border focus:ring-transparent focus:border-gray-300"
                required>
                <option value="" >Select</option>
               ${resultCountry.map(item=>{
                   return `<option value="${item.country_code}" ${(obj[0].country == item.country_code) ? 'selected' : ''}>${item.country} </option>`
               }).join('')}
            </select>
        
        </p>

        <button id="btnCustDetSave" class="mt-4 text-white bg-yellow-500 px-4 focus:outline-none hover:bg-yellow-600 rounded text-md" >Save</button>
        <button id="btnCustDetCancel" class="mt-4 text-white bg-gray-500 px-4 focus:outline-none hover:bg-gray-600 rounded text-md" >Cancel</button>
    </form>
    `;

    document.querySelector('#divCustomerDetails').innerHTML = html;

    document.querySelector('#country').addEventListener('change', async e=>{
        let cities = await getCitiesByCountry(e.target.value);
        let html = cities.map(item=>{
            return `
            <option value="${item.cities}">${item.cities} </option>
            `;
        }).join('');
        document.querySelector('#city').innerHTML = html;
    });

    document.querySelector('#btnCustDetCancel').addEventListener('click', e=>{
        let html = `
            <p class="text-sm"><strong>Order Number:</strong> ${obj[0].order_number}</p>
            <p class="text-sm"><strong>Customer Name:</strong> ${obj[0].customer_name}</p>
            <p class="text-sm"><strong>Customer Contact:</strong> ${obj[0].customer_contact}</p>
            <p class="text-sm"><strong>Shipping Address 1:</strong> ${obj[0].shipping_address1}</p>
            <p class="text-sm"><strong>Shipping Address 2:</strong> ${(obj[0].shipping_address2 !== null) ? obj[0].shipping_address2 : ''}</p>
            <p class="text-sm"><strong>State / Province:</strong> ${(obj[0].state !== null) ? obj[0].state : ''}</p>
            <p class="text-sm"><strong>Postal/Zip: </strong> ${(obj[0].postal !== null) ? obj[0].postal : ''}</p>
            <p class="text-sm"><strong>City:</strong> ${obj[0].city}</p>
            <p class="text-sm"><strong>Country:</strong> ${obj[0].country}</p>
        `;

        document.querySelector('#divCustomerDetails').innerHTML = html;
    });

    document.querySelector('#btnCustDetSave').addEventListener('click', e=>{
        let form = new FormData(document.querySelector('#frmCustDetail'));
        let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        form.append('_token', token);
        form.append('orderID', orderID);

        fetch('/ab-ajax/save_customer_detail',{
            method: 'POST',
            body: form
        })
        .then(res=>res.json())
        .then(result=>{
            if(result.type !== 'error')
            {
                let html = `
                    <p class="text-sm"><strong>Order Number:</strong> ${obj[0].order_number}</p>
                    <p class="text-sm"><strong>Customer Name:</strong> ${result.customerName}</p>
                    <p class="text-sm"><strong>Customer Contact:</strong> ${result.customerContact}</p>
                    <p class="text-sm"><strong>Shipping Address 1:</strong> ${result.shipping_address1}</p>
                    <p class="text-sm"><strong>Shipping Address 2:</strong> ${(result.shipping_address2 !== null) ? result.shipping_address2 : ''}</p>
                    <p class="text-sm"><strong>State / Province:</strong> ${(result.state !== null) ? result.state : ''}</p>
                    <p class="text-sm"><strong>Postal/Zip: </strong> ${(result.postal !== null) ? result.postal : ''}</p>
                    <p class="text-sm"><strong>City:</strong> ${result.city}</p>
                    <p class="text-sm"><strong>Country:</strong> ${result.country}</p>
                `;

                document.querySelector('#divCustomerDetails').innerHTML = html;
            }   
            else {
                alertify.alert(result.msg);
            }
        })
        .catch(e=>console.log(e));
       
    });
};

const editShipmentDetail = (orderID, shipping_carrier, tracking_no, total_weight, total_vol_weight, package_length, package_width, package_height) => {
    let html = `
    <form id="frmShipDetail" onsubmit="return false;">
        <p class="text-sm"><strong>Shipping Carrier:</strong> ${shipping_carrier}</p>
        <p class="text-sm"><strong>Tracking Number:</strong> ${tracking_no}</p>
        <p class="text-sm"><strong>Total Weight:</strong> ${total_weight}KG</p>
        <p class="text-sm"><strong>Total Volumetric Weight:</strong> <input type="text" name="totalVolWeight" id="totalVolWeight"
        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
        value="${total_vol_weight}Kg"
        readonly> </p>
        <p class="text-sm"><strong>Package Dimensions:</strong>
            <select id="shipping_package_size" name="shipping_package_size"
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-yellow-600 focus:border-yellow-600 sm:text-sm"
            onchange="loadPackageDimension(this.value)" required>
                <option value="">Select</option>
                <option value="12X25X16">L:12cm W:25cm H:16cm (1Kg)</option>
                <option value="30X22X15">L:30cm W:22cm H:15cm (2Kg)</option>
                <option value="30X44X18.5">L:30cm W:44cm H:18.5cm (5Kg)</option>
                <option value="other">Other</option>
            </select>
        </p>

        <div class="flex mt-4">
            <div class="relative">
                <label for="length" class="block text-sm text-center font-medium text-gray-700">L*</label>
                <input type="text" name="package_length" id="package_length"
                    class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    required>
            </div>
            <div class="relative mx-3">
                <label for="width" class="block text-sm text-center font-medium text-gray-700">W*</label>
                <input type="text" name="package_width" id="package_width"
                    class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    required>
            </div>
            <div class="relative">
                <label for="height" class="block text-sm text-center font-medium text-gray-700">H*</label>
                <input type="text" name="package_height" id="package_height"
                    class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    required>
            </div>
        </div>
        <button id="btnShipDetSave" class="mt-4 text-white bg-yellow-500 px-4 focus:outline-none hover:bg-yellow-600 rounded text-md" >Save</button>
        <button id="btnShipDetCancel" class="mt-4 text-white bg-gray-500 px-4 focus:outline-none hover:bg-gray-600 rounded text-md" >Cancel</button>
    </form>
    `;

    document.querySelector('#divShippingDetails').innerHTML = html;

    document.querySelector('#btnShipDetCancel').addEventListener('click', e=>{
        let html = `
            <p class="text-sm"><strong>Shipping Carrier:</strong> ${shipping_carrier}</p>
            <p class="text-sm"><strong>Tracking Number:</strong> ${tracking_no}</p>
            <p class="text-sm"><strong>Total Weight:</strong> ${total_weight}KG</p>
            <p class="text-sm"><strong>Total Volumetric Weight:</strong> ${total_vol_weight}Kg</p>
            <p class="text-sm"><strong>Package Dimensions:</strong> L: ${package_length}cm X W: ${package_width}cm X H: ${package_height}cm</p>
        `;

        document.querySelector('#divShippingDetails').innerHTML = html;
    });

    document.querySelector('#package_length').addEventListener('focusout', calculateVolWeight);
    document.querySelector('#package_width').addEventListener('focusout', calculateVolWeight);
    document.querySelector('#package_height').addEventListener('focusout', calculateVolWeight);

    document.querySelector('#btnShipDetSave').addEventListener('click', e=>{
        let form = new FormData(document.querySelector('#frmShipDetail'));
        let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        form.append('_token', token);
        form.append('orderID', orderID);

        fetch('/ab-ajax/save_shipping_detail',{
            method: 'POST',
            body: form
        })
        .then(res=>res.json())
        .then(result=>{
            if(result.type !== 'error')
            {
                console.log(result);
                let html = `
                    <p class="text-sm"><strong>Shipping Carrier:</strong> ${shipping_carrier}</p>
                    <p class="text-sm"><strong>Tracking Number:</strong> ${tracking_no}</p>
                    <p class="text-sm"><strong>Total Weight:</strong> ${total_weight}KG</p>
                    <p class="text-sm"><strong>Total Volumetric Weight:</strong> ${result.totalVolWeight}Kg</p>
                    <p class="text-sm"><strong>Package Dimensions:</strong> L: ${result.package_length}cm X W: ${result.package_width}cm X H: ${result.package_height}cm</p>
                `;

                document.querySelector('#divShippingDetails').innerHTML = html;
            }   
            else {
                alertify.alert(result.msg);
            }
        })
        .catch(e=>console.log(e));
    });
};


const getCountries = async () => {
    let form = new FormData();
    form.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
    let resCountry  = await fetch('/ab-ajax/getCountries', {method: 'POST', body: form});
    let resultCountry = await resCountry.json();

    return resultCountry;
};


const getCitiesByCountry = async (country) => {
    let form = new FormData();
    form.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
    form.append('country', country);

    let resCity = await fetch('/ab-ajax/getCitiesByCountry', {method: 'POST', body: form});
    let resultCity = await resCity.json();

    return resultCity;
};

const create_label = (orderID, order_number) => {
    let form = new FormData();
    form.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
    form.append('orderID', orderID);

    fetch('/ab-ajax/createLabelAndShipOrder', {
        method: 'POST', 
        body: form
    })
    .then(res=>{
        // const contentType = res.headers.get("content-type");
        // if (contentType && contentType.indexOf("application/json") !== -1) {
        //     return res.json().then(data => {
        //       // process your JSON data further
        //       alertify.alert(data.msg);

        //     });
        //   } else {
        //     return res.blob().then(blob => {
              
        //         const url = window.URL.createObjectURL(blob);
        //         const a = document.createElement('a');
        //         a.style.display = 'none';
        //         a.href = url;
        //         if(blob.type == "application/zip"){
        //             a.download = order_number+'_'+getCurrentDate()+'.zip';
        //         } else {
        //             a.download = order_number+'_'+getCurrentDate()+'.pdf';
        //         }
        //         document.body.appendChild(a);
        //         a.click();
        //         window.URL.revokeObjectURL(url);
        //         window.location.reload();
                
        //     });
        //   }
    });
        
    // .then(result=>console.log(result));
    // .then(res=>res.blob()).then(blob=>{
        // const url = window.URL.createObjectURL(blob);
        // const a = document.createElement('a');
        // a.style.display = 'none';
        // a.href = url;
        // if(blob.type == "application/zip"){
        //     a.download = order_number+'_'+getCurrentDate()+'.zip';
        // } else {
        //     a.download = order_number+'_'+getCurrentDate()+'.pdf';
        // }
        // document.body.appendChild(a);
        // a.click();
        // window.URL.revokeObjectURL(url);
        // window.location.reload();
        
    // }).catch((e) => console.log(e));

    
};

const viewOrder = async orderID=>{
    let form = new FormData();
    let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    form.append('_token', token);
    form.append('orderID', orderID);

    let response = await fetch('/ab-ajax/getShipOrderDetails', {method: 'POST', body: form});

    let result = await response.json();

    if(result.type == 'error')
    {
        alertify.alert(result.msg);
        return;
    }
    //${orderID}, '${result[0].order_number}', '${result[0].customer_name}', '${result[0].customer_contact}', '${result[0].shipping_address1}
    
    let html = `
        <div id="ViewOrderPopup">
            <div class="header mb-8"><h1 class="text-center text-2xl uppercase font-bold text-yellow-500">Order Details</h1></div>
            <div class="flex flex-col">
                <div class="flex flex-row item-center justify-between">
                    <h1 class="font-bold text-lg">Customer Details:</h1>
                </div>
                <div id="divCustomerDetails" class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Order Number:</strong> ${result[0].order_number}</p>
                    <p class="text-sm"><strong>Customer Name:</strong> ${result[0].customer_name}</p>
                    <p class="text-sm"><strong>Customer Contact:</strong> ${result[0].customer_contact}</p>
                    <p class="text-sm"><strong>Shipping Address 1:</strong> ${result[0].shipping_address1}</p>
                    <p class="text-sm"><strong>Shipping Address 2:</strong> ${(result[0].shipping_address2 !== null) ? result[0].shipping_address2 : ''}</p>
                    <p class="text-sm"><strong>State / Province:</strong> ${(result[0].state !== null) ? result[0].state : ''}</p>
                    <p class="text-sm"><strong>Postal/Zip</strong> ${(result[0].postal !== null) ? result[0].postal : ''}</p>
                    <p class="text-sm"><strong>City:</strong> ${result[0].city}</p>
                    <p class="text-sm"><strong>Country:</strong> ${result[0].country}</p>
                </div>

                <div><h1 class="font-bold text-lg">Payment Details: </h1></div>
                <div class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Payment Method:</strong> ${result[0].payment_method}</p>
                </div>
                <div class="flex flex-row item-center justify-between">
                    <h1 class="font-bold text-lg">Shipment Details: </h1>
                </div>
                <div id="divShippingDetails" class="relative mb-5 border-yellow-500 p-4 border-2 w-full rounded" >
                    <p class="text-sm"><strong>Shipping Carrier:</strong> ${result[0].shipping_carrier}</p>
                    <p class="text-sm"><strong>Tracking Number:</strong> ${result[0].tracking_no}</p>
                    <p class="text-sm"><strong>Total Weight:</strong> ${result[0].total_weight}KG</p>
                    <p class="text-sm"><strong>Total Volumetric Weight:</strong> ${result[0].total_vol_weight}Kg</p>
                    <p class="text-sm"><strong>Package Dimensions:</strong> L: ${result[0].package_length}cm X W: ${result[0].package_width}cm X H: ${result[0].package_height}cm</p>
                </div>
          
                <div><h1 class="font-bold text-lg">Ordered Products: </h1></div>
                <div class="relative mb-5 border-yellow-500 p-4 border-2 w-full overflow-x-auto rounded" >
                    <table class="table-auto w-full">
                    <tbody>
                        ${result.map(getproductHTMLForShipOrderPOPup).join('')}
                    </tbody>
                    </table>
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
    };
    let viewDialog = makeDialog(html, options);

};

const productInfo = async (product_id)=>{
    let form = new FormData();
    let token = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    form.append('_token', token);
    form.append('product_id', product_id);

    let response = await fetch('/ab-ajax/getProductInfo', {method: 'POST', body: form});

    let result = await response.json();

    if(result.type == 'error')
    {
        alertify.alert(result.msg);
        return;
    }

    if(result.length == 0 )
    {
        alertify.alert('No details found');
        return;
    }

    console.log(result);
    let html = `
        <div id="ViewOrderPopup">
            <div class="header mb-8">
                <h1 class="text-center text-2xl uppercase font-bold text-yellow-500"> ${result[0].label} </h1>
                <p class="text-center">SKU: ${result[0].sku}</p>
            </div>
            <div class="flex flex-col">
                <div class="flex flex-row item-center justify-between">
                    <h1 class="font-bold text-lg">Available Stock: ${result[0].available_stock.available_qty}</h1>
                </div>

                <div class="my-4"><h1 class="font-bold text-lg">Warehouse Bins: </h1></div>
                <table id="tblPinfo" class="table-fixed">
                    <thead>
                    <tr class="border-b-2">
                        <th class="w-1/6 p-2">Bin Location</th>
                        <th class="w-1/6 p-2">Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                        ${ result[0].bins.map(bin=>{
                            return `
                                <tr class="border-b-2">
                                    <td class="p-2 text-center">${ bin.bin_location }</td>
                                    <td class="p-2 text-center"> ${bin.inventory[0].quantity} </td>
                                </tr>
                            `;
                        }).join('') }
                        
                    </tbody>
                
                </table>
                
            </div>
        </div>
    `;

    let options = {
        basic:true,
        maximizable:false,
        resizable:false,
        padding:true, 
        closableByDimmer: false,
    };
    let pinfoDialog = makeDialog(html, options);
}