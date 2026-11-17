<script>

  $(function () {

    $('.transaction').DataTable()
    $('.supplier').DataTable()
    $('.loanProvider').DataTable()
    $('.banktrans').DataTable()
    $('.expense').DataTable()
    $('.othersales').DataTable()
    $('.employeeSalary').DataTable()
    $('.vatpay').DataTable()
    $('.asstest').DataTable()

    var ladgerTable = $('#example1').DataTable({
      "pageLength": 10,
      "order": [ 0, 'asc' ],
      'ordering'    : true,
    })
    ladgerTable.page('last').draw('page');

    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
    })


      // $('#customer').DataTable();
      // $('#supplier').DataTable();
      // $('#account').DataTable();
      // $('#fund').DataTable();
      // $('#expense').DataTable();
      // $('#other').DataTable();
      // $('#employee').DataTable();
      // $('#vat').DataTable();


  })

// This is for go Back Button at the top -- Start --
function goBack() {
  window.history.back();
}
// This is for go Back Button at the top -- End --

</script>

<script type="text/javascript">
  //sticky menu (start) navbar-fixed-top
  $(window).scroll(function() {
    if ($(this).scrollTop() > 25) {
      $('#stickyNav').show();
    }
  });
  //sticky menu (end)


</script>

<script type="text/javascript">
    //select and search option script (start)
    $(function () {
      //Initialize Select2 Elements
      $('.select2').select2();
    });
    //select and search option script (end)

    //These script is for product purchase module (start)
      function addCart(){
        var category = $('[name=category]').val();
        var subCatId = $('[name=sub_category]').val();
        var name = $('[name=name]').val();
        var unit = $('[name=unit]').val();
        var price = $('[name=price]').val();
        var salePrice = $('[name=selling_price]').val();
        var qty = $('[name=qty]').val();

        // var specialChars = "<>@!#$%^&*()_+[]{}?:;|'\"\\,./~`="
        // var check = function(string){
        //       for(i = 0; i < specialChars.length;i++){
        //           if(string.indexOf(specialChars[i]) > -1){
        //               return true
        //           }
        //       }
        //       return false;
        //   }
        //
        // if(check($('[name=name]').val()) == false){
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Admin/Purchase/addCart') ?>",
                dataType: "text",
                data: {subCatId: subCatId,name:name,price:price,salePrice:salePrice,qty:qty,category:category,unit:unit},
                success: function(msg){
                    // alert(msg);
                    $('#message').html(msg);
                    location.reload();
                }
            });
        // }else{
        //     $('#nameValid').html('Illegal characters');
        // }

          
          
          
      }

      function remove_cart(Id){
        var Id = $(Id).attr("id");

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Purchase/remove_cart') ?>",
            dataType: "text",
            data: {id:Id},
            success: function(msg){
              location.reload();
            }
          });
      }
      function clearCart(){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Purchase/clearCart') ?>",
            dataType: "text",
            success: function(msg){
              location.reload();
            }
          });
      }

      function showSubCategory(id){
        //alert(id);
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Purchase/check_sub_cat') ?>",
          dataType: "text",
          data: {ID: id},

          beforeSend: function(){
           $('#subCat').html('<img src="<?php print base_url(); ?>/assets/images/loading.gif" width="20" alt="loading"/> Progressing...');
          },
          success: function(msg){
            $('#subCat').html(msg);
          }
        });
      }
      //due amount calculete (start)
      function totalDue(){
        var sub ;
        var totalPrice = $('#totalPrice').val();
        var cash = $("#cash").val();
        var bank = $("#bank").val();
        sub = (totalPrice-cash)-bank;
        $("#totaldue").val(sub);
        $("#totalAmountDue").val(sub);
      }
      $(document).on( 'input', '.cash', function(){ totalDue(); } );
      $(document).on( 'input', '.bank', function(){ totalDue(); } );
      //due amount calculete (end)

      // These script is for product purchase module -- End ---
    </script>

<script type="text/javascript">
      // These script is for product purchase Exixting Itame -- Start ---
      function calculateExixPrice() {
          var totalPrice = 0;

          var n = $("input.datatables").length;
          var prod_id = $("input[name^='prod_id']");
          var qnt = $("input[name^='quantity']");
          var pur_price = $("input[name^='purchase_price']");

          for(i=0;i<n;i++)
          {
              if($('input.datatables')[i].checked) {
                  quantity =  qnt[i].value;
                  purchase_price = pur_price[i].value;
                  itemPrice = purchase_price * quantity;
                  totalPrice = totalPrice + itemPrice;
              }
          }
          $("#totalAmount").val(totalPrice);
          $("#totalDueAmount").val(totalPrice);

      }

        $(document).on( 'input', '.datatables', function(){ calculateExixPrice(); } );
        $(document).on( 'input', '.quantity', function(){ calculateExixPrice(); } );
        $(document).on( 'input', '.purchase_price', function(){ calculateExixPrice(); } );

        function duetotal(){
        var sub ;
        var totalAmount = $('#totalAmount').val();
        var cash = $("#cash").val();
        var bank = $("#bank").val();
        sub = (totalAmount-cash)-bank;

        $("#totalDueAmount").val(sub);
      }
      $(document).on( 'input', '.cash', function(){ duetotal(); } );
      $(document).on( 'input', '.bank', function(){ duetotal(); } );
       // These script is for product purchase Exixting Itame -- End ---
    </script>


<script type="text/javascript">
  //purchase check cash or bank balance (start)
  function checkShopsBalance(cash){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Purchase/check_shop_balance') ?>",
      dataType: "text",
      data: {cash: cash},

      beforeSend: function(){
       $('#Balance_valid').css( 'color','#238A09');
       $('#Balance_valid').html('<img src="<?php print base_url(); ?>/uploads/loading.gif" width="20" alt="loading" > Progressing...');
      },
      success: function(msg){
          if (msg == 1){
              $('#Balance_valid').html('<span style="color:green">Balance is ok</span>');
              document.getElementById("createBtn").disabled = false;
          }
          if (msg == 0){
              $('#Balance_valid').html('<span style="color:red">Balance is too low</span>');
              document.getElementById("createBtn").disabled = true;
          }

      }
    });
  }
  ////purchase check bank balance
  function checkBankBalance(amount){
    var bankId = $('#bank_id').val();
    //alert(bankId);

      if (bankId == 0) {
        $('#Bank_valid').html('<span style="color:red">Please Select Bank</span>');
        $("#bank").val('');
      }else{
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Purchase/check_bank_balance') ?>",
          dataType: "text",
          data: {balance: amount,bank_id: bankId},

          beforeSend: function(){
           $('#Bank_valid').css( 'color','#238A09');
           $('#Bank_valid').html('<img src="<?php print base_url(); ?>/uploads/loading.gif" width="20" alt="loading"/> Progressing...');
          },
          success: function(msg){
              if (msg == 1){
                  $('#Bank_valid').html('<span style="color:green">Balance is ok</span>');
                  document.getElementById("createBtn").disabled = false;
              }
              if (msg == 0){
                  $('#Bank_valid').html('<span style="color:red">Balance is too low</span>');
                  document.getElementById("createBtn").disabled = true;
              }
          }
        });
      }

  }
  //purchase check cash or bank balance (end)
</script>
<script type="text/javascript">
  //Money receipt customer select option create (start)
  function checkCustomer(id){
    var view ='';
    if (id == 1) {
       view ='<div class="form-group"><label for="varchar">Existing Customer</label><select class="form-control" name="customer_id"><option>Please Select</option><?php echo getAllListInOption('customer_id','customer_id','customer_name','customers'); ?></select></div>';
        $("#data").html(view).show();
    }else{

      view ='<div class="form-group"><label for="varchar">New Customer</label><input type="text" class="form-control" name="name" id="name" placeholder="Name" value="" /></div>';

      $("#data").html(view).show();
    }
  }
   //Money receipt customer select option create (end)
</script>

<script type="text/javascript">

  //transaction bank check (start)
  function checkBank(id){
    if (id == 1) {
      var view ='<div class="form-group "><label for="varchar">Bank</label><select class="form-control input" name="bank_id" required><option value="">Please select</option><?php echo getTwoValueInOption('bank_id','bank_id','name','account_no','bank'); ?></select></div><div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" required /> </div>';

      $(".databank").html(view).show();
      $("#databankSup").html(view).show();
      $("#databankLc").html(view).show();
      $("#dataexpense").html(view).show();
      $("#databankloPo").html(view).show();
      $("#employee").html(view).show();
      $("#vatpayId").html(view).show();
      $("#vatpayId").html(view).show();
    }

    if (id == 3) {
      var view2 ='<div class="form-group" ><label>Cheque</label><input type="text" class="form-control input" name="chequeNo" id="chequeNo" placeholder="Input Cheque No "></div><div class="form-group" ><label>Cheque Amount</label><input type="number" step=any onchange="cheque()" class="form-control chequeAmount input" oninput="minusValueCheck(this.value,this)" name="chequeAmount" id="chequeAmount" placeholder="Input Cheque Amount " required ><b id="cheque_valid"></b></div>';
      $("#chaque").html(view2).show();
      $("#databankSup").html(view2).show();
      $("#databankloPo").html(view2).show();
    }

    if (id == 2) {
      var view3 ='<div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control input" oninput="minusValueCheck(this.value,this)" name="amount" id="amount" placeholder="Amount" required /> </div>';
      $(".databank").html(view3).show();
      $("#databankSup").html(view3).show();
      $("#databankloPo").html(view3).show();
      $("#databankLc").html(view3).show();
      $("#dataexpense").html(view3).show();
      $("#employee").html(view3).show();
      $("#vatpayId").html(view3).show();

    }
  }

  function checkBank2(id){
    if (id == 1) {
       var view ='<div class="form-group"><label for="varchar">Bank </label><select class="form-control" name="bank_id" required><option value="">Please select</option><?php echo getTwoValueInOption('bank_id','bank_id','name','account_no','bank'); ?></select></div><div class="form-group" id="chaque"><label for="int">Amount</label><input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" required /> </div>';
      $("#databankloPo").html(view).show();
    }

    if (id == 3) {
      var view2 ='<div class="form-group" ><label>Cheque</label><input type="text" class="form-control" name="chequeNo" id="chequeNo" placeholder="Input Cheque No "></div><div class="form-group" ><label>Cheque Amount</label><input type="number" step=any onchange="cheque()" class="form-control chequeAmount" name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount " required ><b id="cheque_valid"></b></div>';
      $("#databankloPo").html(view2).show();
    }

    if (id == 2) {
      var view3 ='<div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control" oninput="minusValueCheck(this.value,this)" name="amount" id="amount" placeholder="Amount" required /> </div>';
      $("#databankloPo").html(view3).show();
    }
  }
 //transaction bank check (end)

 //transaction account holder payment type change (start)
 function changepaymenttype(Id)
 {
    if (Id == 2) {
      var view = '<div class="form-group"><label for="payment_type">Payment Type </label><select class="form-control" onchange="checkBank(this.value)" name="payment_type" required><option value="" >Please Select</option><option value="1"  >Bank</option><option value="3"  >Chaque</option><option value="2" selected>Cash</option></select></div>'
        $("#paymentloPo").html(view).show();
        $("#paymentCus").html(view).show();
        $("#paymentsup").html(view).show();
    }else{
      view ='<div class="form-group" ><label for="payment_type">Payment Type </label><select class="form-control" onchange="checkBank(this.value)" name="payment_type" required><option value="" >Please Select</option><option value="1"  >Chaque/Bank</option><option value="2" selected >Cash</option></select></div>';

      $("#paymentloPo").html(view).show();
      $("#paymentCus").html(view).show();
      $("#paymentsup").html(view).show();
    }
 }
 //transaction account holder payment type change (end)
</script>
<script type="text/javascript">
//All ledger view script (start)

  //supplier ledger view (start)
  function supplTransView(Id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/suppData') ?>",
        data: {suppId: Id},
        success: function(html){
          $("#suppData").html(html).show();
        }
      });
  }
  //supplier ledger view (end)

  //customer ledger view (start)
  function custoTranDet(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/custData') ?>",
      data: {custId: Id},
      success: function(html){
        $("#custData").html(html).show();
      }
    });
  }
  //customer ledger view (end)

  //loan provider ledger view (start)
  function lonProvTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/lonProvData') ?>",
      data: {lonProvId: Id},
      success: function(html){
        $("#lonProvData").html(html).show();
      }
    });
  }
  //loan provider ledger view (end)


  //lc ledger view (start)
  function lcTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('transaction/lcData') ?>",
      data: {lcId: Id},
      success: function(html){
        $("#lcData").html(html).show();
      }
    });
  }
  //lc ledger view (end)


  //bank ledger view (start)
  function bankTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/bankData') ?>",
      data: {bankId: Id},
      success: function(html){
        $("#bankData").html(html).show();
      }
    });
  }
  //bank ledger view (end)

  //All ledger view script (end)
</script>

  <script type="text/javascript">
    //salse moduels search product (start)
      function findResult(explicitVal){
          var search_text = (explicitVal !== undefined && typeof explicitVal === 'string') ? explicitVal : $('#keyWord').val();
          if (search_text === undefined || search_text === null) {
              search_text = $('#keyWord').val() || '';
          }
          search_text = $.trim(search_text);
          var sale_save_id = $('#sale_save_id').val();
          if(search_text==""){
              $('#result').empty();
          }else{
              $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('Admin/Sales/search_prod') ?>",
                  data: {keyWord: search_text, sale_save_id: sale_save_id},

                  success: function(html){
                      $("#result").html(html).show();
                  }
              });
          }
      }
      //sales modules search product (end)

      $(document).on('keydown', '#keyWord', function(e) {
          if (e.which === 13 || e.keyCode === 13) {
              e.preventDefault();
              if (typeof findResult === 'function') {
                  findResult($(this).val());
              }
              if (typeof findResultPurchase === 'function') {
                  findResultPurchase($(this).val());
              }
              return false;
          }
      });
</script>

<script type="text/javascript">
    //lc sales modules search product (start)
      function lcSale(){
        $('#keyWord').on('keyup',function(){
          var search_text = $('#keyWord').val();
          if(search_text==""){
                $('#resultpro').empty();
            }else{
                $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('lc_sales/search_prod') ?>",
                  data: {keyWord: search_text},

                  success: function(html){

                    $("#resultpro").html(html).show();
                  }
                });
            }
          });
      }
      //lc sales modules search product (end)
</script>

<script type="text/javascript">
    //return moduels search product (start)
      function returnFindResult(){
        $('#keyWord').on('keyup',function(){
          var search_text = $('#keyWord').val();
          if(search_text==""){
                $('#result').empty();
            }else{
                $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('return_sale/search_prod') ?>",
                  data: {keyWord: search_text},

                  success: function(html){

                    $("#result").html(html).show();
                  }
                });
            }
          });
      }
      //return moduels search product (end)

</script>

<!-- customerLedg list -->
<script type="text/javascript">

  //customer ledger search (start)
  function customerLedg(id){
      var customerId = (id !== undefined && id !== null && id !== '') ? id : $('#customerId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(customerId == '' || customerId == null){
        return false;
      }

      if($.fn.DataTable.isDataTable('#example1')){
        $('#example1').DataTable().destroy();
      }

      $.ajax({
        type: "GET",
        url: "<?php echo site_url('Admin/Ledger/search_customerLedg') ?>",
        data: {customer_id: customerId, st_date: st_date, en_date: en_date},
        success: function(html){
          var target = ($('#customerLedgerResult').length) ? $('#customerLedgerResult') : $('#customerLedg');
          target.html(html).show();
          syncCustomerLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
          updateCustomerLedgerUrl();
        }
      });
  }

  function customerLedgPrint(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger/search_customerLedgPrint') ?>",
        data: {id: id},
        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //customer ledger search (end)


  //customer ledger filter without page reload(start)
  function customerLedgerFilter(){
      var customerId = $('#customerId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(customerId == '' || customerId == null){
        if($.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#customerLedgerResult").html('');
        $("#ledgPrint").html('');
        updateCustomerLedgerUrl();
        return false;
      }

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger/search_dateTodate') ?>",
        data: {customerId: customerId, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#customerLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          $("#customerLedgerResult").html(html);
          syncCustomerLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
          updateCustomerLedgerUrl();
        }
      });
  }

  function resetCustomerLedgerFilter(){
    $('#customerId').val('').trigger('change');
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#customerLedgerResult").html('');
    $("#ledgPrint").html('');
    updateCustomerLedgerUrl();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncCustomerLedgerPrint(){
    var printHtml = $('#ajaxCustLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxCustLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateCustomerLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var customerId = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger') ?>';
    var params = [];
    if(customerId){ params.push('customer_id=' + encodeURIComponent(customerId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //customer ledger filter without page reload(end)


  //bank ledger search (start)
  function bankLedg(id){
      var bankId = (id !== undefined && id !== null && id !== '') ? id : $('#bankId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(bankId == '' || bankId == null){
        return false;
      }

      if($.fn.DataTable.isDataTable('#example1')){
        $('#example1').DataTable().destroy();
      }

      $.ajax({
        type: "GET",
        url: "<?php echo site_url('Admin/Ledger_bank/search_bankLedg') ?>",
        data: {bank_id: bankId, st_date: st_date, en_date: en_date},

        success: function(html){

          var target = ($('#bankLedgerResult').length) ? $('#bankLedgerResult') : $('#bankLedg');
          target.html(html).show();
          syncBankLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');
              updateBankLedgerUrl();
        }
      });
  }

  function bankLedgPrint(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_bank/search_bankLedgPrint') ?>",
        data: {id: id},

        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //bank ledger search (end)



  //bank ledger filter without page reload(start)
  function bankLedgerFilter(){
      var bankId = $('#bankId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(bankId == '' || bankId == null){
        if($.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#bankLedgerResult").html('');
        $("#ledgPrint").html('');
        updateBankLedgerUrl();
        return false;
      }

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_bank/search_dateTodate') ?>",
        data: {bankId: bankId, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#bankLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          $("#bankLedgerResult").html(html);
          syncBankLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');
              updateBankLedgerUrl();
        }
      });
  }

  function resetBankLedgerFilter(){
    $('#bankId').val('').trigger('change');
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#bankLedgerResult").html('');
    $("#ledgPrint").html('');
    updateBankLedgerUrl();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncBankLedgerPrint(){
    var printHtml = $('#ajaxBankLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxBankLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateBankLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var bankId = $('#bankId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_bank') ?>';
    var params = [];
    if(bankId){ params.push('bank_id=' + encodeURIComponent(bankId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //bank ledger filter without page reload(end)



  //bank daily ledger search(start)
  function bankLedgdaily(id){
      var date = $(".date").val();
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Daily_book/search_bankLedg') ?>",
        data: {id:id, date:date},
        success: function(html){
          $("#bankdaileyLedg").html(html).show();
        }
      });
  }
  //bank daily ledger search(end)



  //loan provider ledger search(start)
  function lonProviLedg(id){
    var loanProId = (id !== undefined && id !== null && id !== '') ? id : $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    if(loanProId == '' || loanProId == null){
      return false;
    }

    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }

    $.ajax({
        type: "GET",
        url: "<?php echo site_url('Admin/Ledger_loan/search_lonProvLedg') ?>",
        data: {loan_pro_id: loanProId, st_date: st_date, en_date: en_date},
        success: function(html){
          var target = ($('#loanLedgerResult').length) ? $('#loanLedgerResult') : $('#lonProvLedg');
          target.html(html).show();
          syncLoanLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');
        }
      });
  }

  function lonProviLedgPrint(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_loan/search_lonProvLedgPrint') ?>",
        data: {id: id},
        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //loan provider ledger search(end)


  //loan provider ledger filter without page reload(start)
  function loanLedgerFilter(){
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    if(loanProId == '' || loanProId == null){
      if($.fn.DataTable.isDataTable('#example1')){
        $('#example1').DataTable().destroy();
      }
      $("#loanLedgerResult").html('');
      $("#ledgPrint").html('');
      updateLoanLedgerUrl();
      return false;
    }

    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Ledger_loan/search_dateTodate') ?>",
      data: {loanProId: loanProId, st_date: st_date, en_date: en_date},
      beforeSend: function(){
        if($.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#loanLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
      },
      success: function(html){
        $("#loanLedgerResult").html(html);
        syncLoanLedgerPrint();
        var ladgerTable = $('#example1').DataTable({
          "pageLength": 10,
          "order": [ 0, 'asc' ],
          'ordering'    : true,
        });
        ladgerTable.page('last').draw('page');
        updateLoanLedgerUrl();
      }
    });
  }

  function resetLoanLedgerFilter(){
    $('#loanProId').val('').trigger('change');
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#loanLedgerResult").html('');
    $("#ledgPrint").html('');
    updateLoanLedgerUrl();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncLoanLedgerPrint(){
    var printHtml = $('#ajaxLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateLoanLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_loan') ?>';
    var params = [];
    if(loanProId){ params.push('loan_pro_id=' + encodeURIComponent(loanProId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //loan provider ledger filter without page reload(end)



  //suppliers ledger search(start)
  function supplierLedgher(id){
    var supplierId = (id !== undefined && id !== null && id !== '') ? id : $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    if(supplierId == '' || supplierId == null){
      return false;
    }

    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }

    $.ajax({
      type: "GET",
      url: "<?php echo site_url('Admin/Ledger_suppliers/search_supplierLedg') ?>",
      data: {supplier_id: supplierId, st_date: st_date, en_date: en_date},
      success: function(html){
        var target = ($('#supplierLedgerResult').length) ? $('#supplierLedgerResult') : $('#supplierLedg');
        target.html(html).show();
        syncSupplierLedgerPrint();
        var ladgerTable = $('#example1').DataTable({
          "pageLength": 10,
          "order": [ 0, 'asc' ],
          'ordering'    : true,
        });
        ladgerTable.page('last').draw('page');
        updateSupplierLedgerUrl();
      }
    });
  }


  function supplierLedgherPrint(id){
    var supplierId = (id !== undefined && id !== null && id !== '') ? id : $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Ledger_suppliers/search_supplierLedgPrint') ?>",
      data: {id: supplierId, st_date: st_date, en_date: en_date},
      success: function(html){
        $("#ledgPrint").html(html).show();
      }
    });
  }
  //suppliers ledger search(end)

  //supplier ledger filter without page reload(start)
  function supplierLedgerFilter(){
      var supplierId = $('#supplierId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(supplierId == '' || supplierId == null){
        if($.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#supplierLedgerResult").html('');
        $("#ledgPrint").html('');
        updateSupplierLedgerUrl();
        return false;
      }

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_suppliers/search_dateTodate') ?>",
        data: {supplierId: supplierId, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#supplierLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#supplierLedgerResult').length) ? $('#supplierLedgerResult') : $('#supplierLedg');
          target.html(html).show();
          syncSupplierLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateSupplierLedgerUrl();
        }
      });
  }

  function resetSupplierLedgerFilter(){
    $('#supplierId').val('').trigger('change');
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#supplierLedgerResult").html('');
    $("#ledgPrint").html('');
    updateSupplierLedgerUrl();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncSupplierLedgerPrint(){
    var printHtml = $('#ajaxSuppLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxSuppLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateSupplierLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var supplierId = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_suppliers') ?>';
    var params = [];
    if(supplierId){ params.push('supplier_id=' + encodeURIComponent(supplierId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //supplier ledger filter without page reload(end)

  //purchase ledger filter without page reload(start)
  function purchaseLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_purchase/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#purchaseLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#purchaseLedgerResult').length) ? $('#purchaseLedgerResult') : $('#purchaseLedg');
          target.html(html).show();
          syncPurchaseLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updatePurchaseLedgerUrl();
        }
      });
  }

  function resetPurchaseLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    purchaseLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncPurchaseLedgerPrint(){
    var printHtml = $('#ajaxPurchLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxPurchLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updatePurchaseLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_purchase') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //purchase ledger filter without page reload(end)

  //sale ledger filter without page reload(start)
  function salesLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_sales/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#salesLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#salesLedgerResult').length) ? $('#salesLedgerResult') : $('#salesLedg');
          target.html(html).show();
          syncSalesLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateSalesLedgerUrl();
        }
      });
  }

  function resetSalesLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    salesLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncSalesLedgerPrint(){
    var printHtml = $('#ajaxSaleLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxSaleLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateSalesLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_sales') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //sale ledger filter without page reload(end)

  //stock ledger filter without page reload(start)
  function stockLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_stock/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#stockLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#stockLedgerResult').length) ? $('#stockLedgerResult') : $('#stockLedg');
          target.html(html).show();
          syncStockLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateStockLedgerUrl();
        }
      });
  }

  function resetStockLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    stockLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncStockLedgerPrint(){
    var printHtml = $('#ajaxStockLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxStockLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateStockLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_stock') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //stock ledger filter without page reload(end)

  //profit ledger filter without page reload(start)
  function profitLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_profit/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#profitLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#profitLedgerResult').length) ? $('#profitLedgerResult') : $('#profitLedg');
          target.html(html).show();
          syncProfitLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateProfitLedgerUrl();
        }
      });
  }

  function resetProfitLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    profitLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncProfitLedgerPrint(){
    var printHtml = $('#ajaxProfitLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxProfitLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateProfitLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_profit') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //profit ledger filter without page reload(end)

  //capital ledger filter without page reload(start)
  function capitalLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_capital/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#capitalLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#capitalLedgerResult').length) ? $('#capitalLedgerResult') : $('#capitalLedg');
          target.html(html).show();
          syncCapitalLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateCapitalLedgerUrl();
        }
      });
  }

  function resetCapitalLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    capitalLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncCapitalLedgerPrint(){
    var printHtml = $('#ajaxCapitalLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxCapitalLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateCapitalLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_capital') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //capital ledger filter without page reload(end)

  //vat ledger filter without page reload(start)
  function vatLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_vat/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#vatLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#vatLedgerResult').length) ? $('#vatLedgerResult') : $('#vatLedg');
          target.html(html).show();
          syncVatLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateVatLedgerUrl();
        }
      });
  }

  function resetVatLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    vatLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncVatLedgerPrint(){
    var printHtml = $('#ajaxVatLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxVatLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateVatLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_vat') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //vat ledger filter without page reload(end)

  //other sales ledger filter without page reload(start)
  function otherSalesLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_other_sales/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#otherSalesLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#otherSalesLedgerResult').length) ? $('#otherSalesLedgerResult') : $('#otherSalesLedg');
          target.html(html).show();
          syncOtherSalesLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateOtherSalesLedgerUrl();
        }
      });
  }

  function resetOtherSalesLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    otherSalesLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncOtherSalesLedgerPrint(){
    var printHtml = $('#ajaxOtherSalesLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxOtherSalesLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateOtherSalesLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_other_sales') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //other sales ledger filter without page reload(end)

  //service charge ledger filter without page reload(start)
  function serviceChargeLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_service_charge/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#serviceChargeLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#serviceChargeLedgerResult').length) ? $('#serviceChargeLedgerResult') : $('#serviceChargeLedg');
          target.html(html).show();
          syncServiceChargeLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateServiceChargeLedgerUrl();
        }
      });
  }

  function resetServiceChargeLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    serviceChargeLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncServiceChargeLedgerPrint(){
    var printHtml = $('#ajaxServiceChargeLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxServiceChargeLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateServiceChargeLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_service_charge') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //service charge ledger filter without page reload(end)

  //discount ledger filter without page reload(start)
  function discountLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_discount/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#discountLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#discountLedgerResult').length) ? $('#discountLedgerResult') : $('#discountLedg');
          target.html(html).show();
          syncDiscountLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateDiscountLedgerUrl();
        }
      });
  }

  function resetDiscountLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    discountLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncDiscountLedgerPrint(){
    var printHtml = $('#ajaxDiscountLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxDiscountLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateDiscountLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_discount') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //discount ledger filter without page reload(end)

  //account ledger filter without page reload(start)
  function accountLedgerFilter(){
      var accountId = $('#accountId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      if(accountId == '' || accountId == null){
        if($.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#accountLedgerResult").html('');
        $("#ledgPrint").html('');
        updateAccountLedgerUrl();
        return false;
      }

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_accounts/search_dateTodate') ?>",
        data: {accountId: accountId, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#accountLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#accountLedgerResult').length) ? $('#accountLedgerResult') : $('#accountLedg');
          target.html(html).show();
          syncAccountLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateAccountLedgerUrl();
        }
      });
  }

  function resetAccountLedgerFilter(){
    $('#accountId').val('').trigger('change');
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#accountLedgerResult").html('');
    $("#ledgPrint").html('');
    updateAccountLedgerUrl();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncAccountLedgerPrint(){
    var printHtml = $('#ajaxAccountLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxAccountLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateAccountLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var accountId = $('#accountId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_accounts') ?>';
    var params = [];
    if(accountId){ params.push('account_id=' + encodeURIComponent(accountId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //account ledger filter without page reload(end)

  //stock transfer filter without page reload(start)
  function stockTransferFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Stock_transfer/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#stockTransferResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#stockTransferResult').length) ? $('#stockTransferResult') : $('#stockTransferLedg');
          target.html(html).show();
          syncStockTransferPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateStockTransferUrl();
        }
      });
  }

  function resetStockTransferFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    stockTransferFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncStockTransferPrint(){
    var printHtml = $('#ajaxStockTransferPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxStockTransferPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateStockTransferUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Stock_transfer') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //stock transfer filter without page reload(end)

  //exchange product filter without page reload(start)
  function exchangeProductFilter(){
      var customer = $('#customerId').val();
      var status = $('#status').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Exchange_product/search_dateTodate') ?>",
        data: {customer: customer, status: status, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#exchangeProductResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#exchangeProductResult').length) ? $('#exchangeProductResult') : $('#exchangeProductLedg');
          target.html(html).show();
          syncExchangeProductPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateExchangeProductUrl();
        }
      });
  }

  function resetExchangeProductFilter(){
    $('#customerId').val('');
    $('#status').val('');
    if($.fn.select2){
      $('#customerId').trigger('change.select2');
      $('#status').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    exchangeProductFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncExchangeProductPrint(){
    var printHtml = $('#ajaxExchangeProductPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxExchangeProductPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateExchangeProductUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var customer = $('#customerId').val();
    var status = $('#status').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Exchange_product') ?>';
    var params = [];
    if(customer){ params.push('customer=' + encodeURIComponent(customer)); }
    if(status){ params.push('status=' + encodeURIComponent(status)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //exchange product filter without page reload(end)

  //return sale filter without page reload(start)
  function returnSaleFilter(){
      var customer = $('#customerId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Return_sale/search_dateTodate') ?>",
        data: {customer: customer, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#returnSaleResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#returnSaleResult').length) ? $('#returnSaleResult') : $('#returnSaleLedg');
          target.html(html).show();
          syncReturnSalePrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateReturnSaleUrl();
        }
      });
  }

  function resetReturnSaleFilter(){
    $('#customerId').val('');
    if($.fn.select2){
      $('#customerId').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    returnSaleFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncReturnSalePrint(){
    var printHtml = $('#ajaxReturnSalePrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxReturnSalePrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateReturnSaleUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var customer = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Return_sale') ?>';
    var params = [];
    if(customer){ params.push('customer=' + encodeURIComponent(customer)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //return sale filter without page reload(end)

  //sales filter without page reload(start)
  function salesFilter(){
      var customer = $('#customerId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Sales/search_dateTodate') ?>",
        data: {customer: customer, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#salesResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#salesResult').length) ? $('#salesResult') : $('#salesLedg');
          target.html(html).show();
          syncSalesPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateSalesUrl();
        }
      });
  }

  function resetSalesFilter(){
    $('#customerId').val('');
    if($.fn.select2){
      $('#customerId').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    salesFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncSalesPrint(){
    var printHtml = $('#ajaxSalesPrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxSalesPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateSalesUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var customer = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Sales') ?>';
    var params = [];
    if(customer){ params.push('customer=' + encodeURIComponent(customer)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //sales filter without page reload(end)

  //service filter without page reload(start)
  function serviceFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Service/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#serviceResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#serviceResult').length) ? $('#serviceResult') : $('#serviceLedg');
          target.html(html).show();
          syncServicePrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateServiceUrl();
        }
      });
  }

  function resetServiceFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    serviceFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncServicePrint(){
    var printHtml = $('#ajaxServicePrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxServicePrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateServiceUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Service') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //service filter without page reload(end)

  //purchase filter without page reload(start)
  function purchaseFilter(){
      var supplier_id = $('#supplierId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Purchase/search_dateTodate') ?>",
        data: {supplier_id: supplier_id, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#purchaseResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#purchaseResult').length) ? $('#purchaseResult') : $('#purchaseLedg');
          target.html(html).show();
          syncPurchasePrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updatePurchaseUrl();
        }
      });
  }

  function resetPurchaseFilter(){
    $('#supplierId').val('');
    if($.fn.select2){
      $('#supplierId').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    purchaseFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncPurchasePrint(){
    var printHtml = $('#ajaxPurchasePrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxPurchasePrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updatePurchaseUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var supplier_id = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Purchase') ?>';
    var params = [];
    if(supplier_id){ params.push('supplier_id=' + encodeURIComponent(supplier_id)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //purchase filter without page reload(end)

  //return purchase filter without page reload(start)
  function returnPurchaseFilter(){
      var supplier_id = $('#supplierId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Return_purchase/search_dateTodate') ?>",
        data: {supplier_id: supplier_id, st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          $("#returnPurchaseResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
        },
        success: function(html){
          var target = ($('#returnPurchaseResult').length) ? $('#returnPurchaseResult') : $('#returnPurchaseLedg');
          target.html(html).show();
          syncReturnPurchasePrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateReturnPurchaseUrl();
        }
      });
  }

  function resetReturnPurchaseFilter(){
    $('#supplierId').val('');
    if($.fn.select2){
      $('#supplierId').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    returnPurchaseFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncReturnPurchasePrint(){
    var printHtml = $('#ajaxReturnPurchasePrint').html();
    if(printHtml !== undefined && printHtml !== null){
      $('#ledgPrint').html(printHtml);
      $('#ajaxReturnPurchasePrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateReturnPurchaseUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var supplier_id = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Return_purchase') ?>';
    var params = [];
    if(supplier_id){ params.push('supplier_id=' + encodeURIComponent(supplier_id)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //return purchase filter without page reload(end)

  //transaction filter without page reload(start)
  function transactionFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var category = $('#hidden_category').val();
      var customer_id = $('#hidden_customer_id').val();
      var supplier_id = $('#hidden_supplier_id').val();
      var loan_pro_id = $('#hidden_loan_pro_id').val();
      var bank_id = $('#hidden_bank_id').val();
      var employee_id = $('#hidden_employee_id').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Transaction/search_dateTodate') ?>",
          data: {
              st_date: st_date,
              en_date: en_date,
              category: category,
              customer_id: customer_id,
              supplier_id: supplier_id,
              loan_pro_id: loan_pro_id,
              bank_id: bank_id,
              employee_id: employee_id
          },
          beforeSend: function(){
              $('#transactionResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#transactionResult').html(html);
              initTransactionDataTables();
              if($.fn.select2){
                  $('#transactionResult .select2').select2();
              }
              updateTransactionUrl();
          },
          error: function(){
              $('#transactionResult').html('<div class="alert alert-danger">Failed to load transaction data.</div>');
          }
      });
  }

  function resetTransactionFilter(){
      $('#st_date').val('');
      $('#en_date').val('');
      $('#hidden_customer_id').val('');
      $('#hidden_supplier_id').val('');
      $('#hidden_loan_pro_id').val('');
      $('#hidden_bank_id').val('');
      $('#hidden_employee_id').val('');
      transactionFilter();
  }

  function initTransactionDataTables(){
      var tables = ['#customer2', '#supplier2', '#account2', '#fund2', '#expense2', '#other2', '#employee2', '#vat2', '#assets1'];
      tables.forEach(function(tblId){
          if($.fn.DataTable && $.fn.DataTable.isDataTable(tblId)){
              $(tblId).DataTable().destroy();
          }
          if($(tblId).length){
              $(tblId).DataTable({
                  "pageLength": 10,
                  "order": [ 0, 'asc' ],
                  "ordering": true
              });
          }
      });
  }

  function updateTransactionUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var category = $('#hidden_category').val();
      var customer_id = $('#hidden_customer_id').val();
      var supplier_id = $('#hidden_supplier_id').val();
      var loan_pro_id = $('#hidden_loan_pro_id').val();
      var bank_id = $('#hidden_bank_id').val();
      var employee_id = $('#hidden_employee_id').val();

      var url = '<?php echo site_url('Admin/Transaction') ?>';
      var params = [];
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
      if(category){ params.push('category=' + encodeURIComponent(category)); }
      if(customer_id){ params.push('customer_id=' + encodeURIComponent(customer_id)); }
      if(supplier_id){ params.push('supplier_id=' + encodeURIComponent(supplier_id)); }
      if(loan_pro_id){ params.push('loan_pro_id=' + encodeURIComponent(loan_pro_id)); }
      if(bank_id){ params.push('bank_id=' + encodeURIComponent(bank_id)); }
      if(employee_id){ params.push('employee_id=' + encodeURIComponent(employee_id)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //transaction filter without page reload(end)

  //product filter without page reload(start)
  function productFilter(){
      var category = $('#category').val();
      var sub_category = $('#subCat').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Products/search_dateTodate') ?>",
          data: {
              category: category,
              sub_category: sub_category,
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#productResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#productResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncProductPrint();
              updateProductUrl();
          },
          error: function(){
              $('#productResult').html('<div class="alert alert-danger">Failed to load product data.</div>');
          }
      });
  }

  function resetProductFilter(){
      $('#category').val('');
      $('#subCat').html('<option value="">Please Select</option>');
      $('#st_date').val('');
      $('#en_date').val('');
      productFilter();
  }

  function syncProductPrint(){
      var printHtml = $('#ajaxProductPrint').html();
      if(printHtml !== undefined && printHtml !== null){
          $('#ledgPrint').html(printHtml);
          $('#ajaxProductPrint').remove();
      }
  }

  function updateProductUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var category = $('#category').val();
      var sub_category = $('#subCat').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Products') ?>';
      var params = [];
      if(category){ params.push('category=' + encodeURIComponent(category)); }
      if(sub_category){ params.push('sub_category=' + encodeURIComponent(sub_category)); }
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //product filter without page reload(end)

  //transaction events filter without page reload(start)
  function transactionEventsFilter(){
      var event_type = $('#eventType').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Transaction_events/search_dateTodate') ?>",
          data: {
              event_type: event_type,
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#transactionEventsResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#transactionEventsResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncTransactionEventsPrint();
              updateTransactionEventsUrl();
          },
          error: function(){
              $('#transactionEventsResult').html('<div class="alert alert-danger">Failed to load transaction events data.</div>');
          }
      });
  }

  function resetTransactionEventsFilter(){
      $('#eventType').val('');
      $('#st_date').val('');
      $('#en_date').val('');
      transactionEventsFilter();
  }

  function syncTransactionEventsPrint(){
      var printHtml = $('#ajaxTransactionEventsPrint').html();
      if(printHtml !== undefined && printHtml !== null){
          $('#ledgPrint').html(printHtml);
          $('#ajaxTransactionEventsPrint').remove();
      }
  }

  function updateTransactionEventsUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var event_type = $('#eventType').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Transaction_events') ?>';
      var params = [];
      if(event_type){ params.push('event_type=' + encodeURIComponent(event_type)); }
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //transaction events filter without page reload(end)

  //income statement filter without page reload(start)
  function incomeStatementFilter(source){
      if(source === 'period'){
          $('#month').val('');
          $('#st_date').val('');
          $('#en_date').val('');
      } else if(source === 'month'){
          $('#period').val('0');
          $('#st_date').val('');
          $('#en_date').val('');
      } else if(source === 'date'){
          $('#period').val('0');
          $('#month').val('');
      }

      var period = $('#period').val();
      var month = $('#month').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Income_statement/search_dateTodate') ?>",
          data: {
              period: period,
              month: month,
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#incomeStatementResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#incomeStatementResult').html(html);
              syncIncomeStatementPrint();
              updateIncomeStatementUrl();
          },
          error: function(){
              $('#incomeStatementResult').html('<div class="alert alert-danger">Failed to load income statement data.</div>');
          }
      });
  }

  function resetIncomeStatementFilter(){
      $('#period').val('30');
      $('#month').val('');
      $('#st_date').val('');
      $('#en_date').val('');
      incomeStatementFilter('period');
  }

  function syncIncomeStatementPrint(){
      var printHtml = $('#ajaxIncomeStatementPrint').html();
      if(printHtml !== undefined && printHtml !== null){
          $('#incomePrint').html(printHtml);
          $('#ajaxIncomeStatementPrint').remove();
      }
  }

  function updateIncomeStatementUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var period = $('#period').val();
      var month = $('#month').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Income_statement') ?>';
      var params = [];
      if(period && period !== '0'){ params.push('period=' + encodeURIComponent(period)); }
      if(month){ params.push('month=' + encodeURIComponent(month)); }
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //income statement filter without page reload(end)

  //sales report filter without page reload(start)
  function salesReportFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var customer_id = $('#customerId').val();
      var prod_id = $('#prodId').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Sales_report/search_dateTodate') ?>",
          data: {
              st_date: st_date,
              en_date: en_date,
              customer_id: customer_id,
              prod_id: prod_id
          },
          beforeSend: function(){
              $('#salesReportResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#salesReportResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncSalesReportPrint();
              updateSalesReportUrl();
          },
          error: function(){
              $('#salesReportResult').html('<div class="alert alert-danger">Failed to load sales report data.</div>');
          }
      });
  }

  function resetSalesReportFilter(){
      $('#st_date').val('');
      $('#en_date').val('');
      $('#customerId').val('');
      if($.fn.select2){
          $('#customerId').trigger('change.select2');
      }
      $('#prodId').val('');
      if($.fn.select2){
          $('#prodId').trigger('change.select2');
      }
      salesReportFilter();
  }

  function syncSalesReportPrint(){
      var printHtml = $('#ajaxSalesReportPrint').html();
      if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
          $('#ledgPrint').html(printHtml);
          $('#ajaxSalesReportPrint').remove();
      }
  }

  function updateSalesReportUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var customer_id = $('#customerId').val();
      var prod_id = $('#prodId').val();

      var url = '<?php echo site_url('Admin/Sales_report') ?>';
      var params = [];
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
      if(customer_id){ params.push('customer_id=' + encodeURIComponent(customer_id)); }
      if(prod_id){ params.push('prod_id=' + encodeURIComponent(prod_id)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //sales report filter without page reload(end)

  //purchase report filter without page reload(start)
  function purchaseReportFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var supplier_id = $('#supplierId').val();
      var prod_id = $('#prodId').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Purchase_report/search_dateTodate') ?>",
          data: {
              st_date: st_date,
              en_date: en_date,
              supplier_id: supplier_id,
              prod_id: prod_id
          },
          beforeSend: function(){
              $('#purchaseReportResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#purchaseReportResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncPurchaseReportPrint();
              updatePurchaseReportUrl();
          },
          error: function(){
              $('#purchaseReportResult').html('<div class="alert alert-danger">Failed to load purchase report data.</div>');
          }
      });
  }

  function resetPurchaseReportFilter(){
      $('#st_date').val('');
      $('#en_date').val('');
      $('#supplierId').val('');
      if($.fn.select2){
          $('#supplierId').trigger('change.select2');
      }
      $('#prodId').val('');
      if($.fn.select2){
          $('#prodId').trigger('change.select2');
      }
      purchaseReportFilter();
  }

  function syncPurchaseReportPrint(){
      var printHtml = $('#ajaxPurchaseReportPrint').html();
      if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
          $('#ledgPrint').html(printHtml);
          $('#ajaxPurchaseReportPrint').remove();
      }
  }

  function updatePurchaseReportUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();
      var supplier_id = $('#supplierId').val();
      var prod_id = $('#prodId').val();

      var url = '<?php echo site_url('Admin/Purchase_report') ?>';
      var params = [];
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
      if(supplier_id){ params.push('supplier_id=' + encodeURIComponent(supplier_id)); }
      if(prod_id){ params.push('prod_id=' + encodeURIComponent(prod_id)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //purchase report filter without page reload(end)

  //employee ledger filter without page reload(start)
  function employeeLedgerFilter(){
    var employeeId = $('#employeeId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    if(employeeId == '' || employeeId == null){
      if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
        $('#example1').DataTable().destroy();
      }
      $("#employeeLedgerResult").html('');
      $("#ledgPrint").html('');
      updateEmployeeLedgerUrl();
      return false;
    }

    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Ledger_employee/search_dateTodate') ?>",
      data: {employeeId: employeeId, st_date: st_date, en_date: en_date},
      beforeSend: function(){
        if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        $("#employeeLedgerResult").html('<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>');
      },
      success: function(html){
        $("#employeeLedgerResult").html(html).show();
        syncEmployeeLedgerPrint();
        if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
          $('#example1').DataTable().destroy();
        }
        if($('#example1').length){
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
        }
        updateEmployeeLedgerUrl();
      },
      error: function(){
        $("#employeeLedgerResult").html('<div class="alert alert-danger">Failed to load employee ledger data.</div>');
      }
    });
  }

  function resetEmployeeLedgerFilter(){
    $('#employeeId').val('');
    if($.fn.select2){
      $('#employeeId').trigger('change.select2');
    }
    $('#st_date').val('');
    $('#en_date').val('');
    if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
      $('#example1').DataTable().destroy();
    }
    $("#employeeLedgerResult").html('');
    $("#ledgPrint").html('');
    updateEmployeeLedgerUrl();
  }

  function syncEmployeeLedgerPrint(){
    var printHtml = $('#ajaxEmpLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
      $('#ledgPrint').html(printHtml);
      $('#ajaxEmpLedgPrint').remove();
    }
  }

  function updateEmployeeLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var employeeId = $('#employeeId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_employee') ?>';
    var params = [];
    if(employeeId){ params.push('employee_id=' + encodeURIComponent(employeeId)); }
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }
  //employee ledger filter without page reload(end)

  //affiliate commission filter without page reload(start)
  function affiliateCommissionFilter(){
      var affiliate_user_id = $('#affiliateUserId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Affiliate_commission/search_dateTodate') ?>",
          data: {
              affiliate_user_id: affiliate_user_id,
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#affiliateCommissionResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#affiliateCommissionResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncAffiliateCommissionPrint();
              updateAffiliateCommissionUrl();
          },
          error: function(){
              $('#affiliateCommissionResult').html('<div class="alert alert-danger">Failed to load commission data.</div>');
          }
      });
  }

  function resetAffiliateCommissionFilter(){
      $('#affiliateUserId').val('');
      if($.fn.select2){
          $('#affiliateUserId').trigger('change.select2');
      }
      $('#st_date').val('');
      $('#en_date').val('');
      affiliateCommissionFilter();
  }

  function syncAffiliateCommissionPrint(){
      var printHtml = $('#ajaxAffiliateCommissionPrint').html();
      if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
          $('#ledgPrint').html(printHtml);
          $('#ajaxAffiliateCommissionPrint').remove();
      }
  }

  function updateAffiliateCommissionUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var affiliate_user_id = $('#affiliateUserId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Affiliate_commission') ?>';
      var params = [];
      if(affiliate_user_id){ params.push('affiliate_user_id=' + encodeURIComponent(affiliate_user_id)); }
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //affiliate commission filter without page reload(end)

  //affiliate commission pay filter without page reload(start)
  function affiliateCommissionPayFilter(){
      var affiliate_user_id = $('#affiliateUserId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Affiliate_commission_pay/search_dateTodate') ?>",
          data: {
              affiliate_user_id: affiliate_user_id,
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#affiliateCommissionPayResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#affiliateCommissionPayResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'asc' ],
                      "ordering": true
                  });
              }
              syncAffiliateCommissionPayPrint();
              updateAffiliateCommissionPayUrl();
          },
          error: function(){
              $('#affiliateCommissionPayResult').html('<div class="alert alert-danger">Failed to load commission pay data.</div>');
          }
      });
  }

  function resetAffiliateCommissionPayFilter(){
      $('#affiliateUserId').val('');
      if($.fn.select2){
          $('#affiliateUserId').trigger('change.select2');
      }
      $('#st_date').val('');
      $('#en_date').val('');
      affiliateCommissionPayFilter();
  }

  function syncAffiliateCommissionPayPrint(){
      var printHtml = $('#ajaxAffiliateCommissionPayPrint').html();
      if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
          $('#ledgPrint').html(printHtml);
          $('#ajaxAffiliateCommissionPayPrint').remove();
      }
  }

  function updateAffiliateCommissionPayUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var affiliate_user_id = $('#affiliateUserId').val();
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Affiliate_commission_pay') ?>';
      var params = [];
      if(affiliate_user_id){ params.push('affiliate_user_id=' + encodeURIComponent(affiliate_user_id)); }
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //affiliate commission pay filter without page reload(end)

  //capital filter without page reload(start)
  function capitalFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Capital/search_dateTodate') ?>",
          data: {
              st_date: st_date,
              en_date: en_date
          },
          beforeSend: function(){
              $('#capitalResult').html('<div class="text-center" style="padding:50px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
          },
          success: function(html){
              $('#capitalResult').html(html);
              if($.fn.DataTable && $.fn.DataTable.isDataTable('#example1')){
                  $('#example1').DataTable().destroy();
              }
              if($('#example1').length){
                  $('#example1').DataTable({
                      "pageLength": 10,
                      "order": [ 0, 'desc' ],
                      'ordering'    : true,
                  });
              }
              syncCapitalPrint();
              updateCapitalUrl();
          },
          error: function(){
              $('#capitalResult').html('<div class="alert alert-danger">Failed to load capital data.</div>');
          }
      });
  }

  function resetCapitalFilter(){
      $('#st_date').val('');
      $('#en_date').val('');
      capitalFilter();
  }

  function syncCapitalPrint(){
      var printHtml = $('#ajaxCapitalPrint').html();
      if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
          $('#ledgPrint').html(printHtml);
          $('#ajaxCapitalPrint').remove();
      }
  }

  function updateCapitalUrl(){
      if(!window.history || !window.history.replaceState){ return; }
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      var url = '<?php echo site_url('Admin/Capital/list') ?>';
      var params = [];
      if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
      if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }

      if(params.length){ url = url + '?' + params.join('&'); }
      window.history.replaceState(null, '', url);
  }
  //capital filter without page reload(end)

   // Lc ledger(start)
   function lcLedgview(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_lcLedg') ?>",
      data: {id: id},
      success: function(html){
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
      }
    });
  }
   // Lc ledger(end)

  function lcLedgPrintview(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_lcLedgPrint') ?>",
      data: {id: id},
      success: function(html){

        $("#lcLedgprint").html(html).show();
      }
    });
  }

   function lcLedSerc(){

    var lc_id = $('#lc_id').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (lc_id == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('ledger_lc/search_datetodate') ?>",
          data: {lcId:lc_id,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#lcLedg").html(val).show();

            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

   function lcLedSercPrint(){

    var lc_id = $('#lc_id').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('ledger_lc/search_datetodatePrint') ?>",
          data: {lcId:lc_id,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#lcLedgprint").html(html).show();
          }
        });
  }

</script>

<script type="text/javascript">
  //all script sales from calculation needed (start)

      function validationDiscount(id){
          var input =  $('#'+id);
          if (input.val() == '' ){
              input.val(0);
          }else{
              input.val(Number(input.val()));
          }
      }

      //single product sale discount calculet(start)
      function prodcalculate() {
          var itemPrice = 0;
          var dicprice2 = 0;
          var n = $("input[name^='disc']").length;
          var dis = $("input[name^='disc']");
          var subto = $("input[name^='subtotal']");
          if ($("input[name^='disc']").val()) {
              for (i = 0; i < n; i++) {
                  discount = dis[i].value;
                  subtotal = subto[i].value;
                  dicprice = subtotal - ((discount * subtotal) / 100);
                  dicprice2 += (discount * subtotal) / 100;
                  itemPrice += dicprice;
                  $("#subtl_" + i).html(dicprice);
                  $("#subtl2_" + i).val(dicprice);
              }
              $("#totalamount").val(itemPrice);
              $("#grandtotaldue").val(itemPrice);
              $("#grandtotal").val(itemPrice);
              $("#grandtotal2").val(itemPrice);
              $("#grandtotallast").val(itemPrice);
              $("#granddiscountlast").val(dicprice2);
          }
        }
      $(document).on( 'input', '.disc', function(){ calculateTotalDiscount(); } );
      //single product sale discount calculet(end)



      //Entire Sale Discount calculet (start)
      function allsalecalculate() {
          var total = 0;

          if ($("#saleDisc").val()) {
              total = (+$("#saleDisc").val() * +$("#totalamount").val()) / 100;
              totalprice = +$("#totalamount").val() - total;
              // yourFloatVarHere
              $("#grandtotal").val(totalprice);
              $("#grandtotaldue").val(totalprice);
              $("#grandtotallast").val(totalprice);
              $("#vatTotallast").val(totalprice);
              $("#saleDiscshow").val(total);
          }else{
              totalprice = +$("#totalamount").val() + total;
              $("#grandtotal").val(totalprice);
              $("#grandtotaldue").val(totalprice);
              $("#grandtotallast").val(totalprice);
              $("#vatTotallast").val(totalprice);
              $("#saleDiscshow").val(total);
          }
      }
      $(document).on( 'input', '.saleDisc', function(){ calculateTotalDiscount(); } );
      //Entire Sale Discount calculet (end)

      //Entire Sale Vat calculet (start)
      function allSaleVatCalculate() {
          var total = 0;
          if ($("#vat").val()) {
              total = (+$("#vat").val() * +$("#vatTotallast").val()) / 100;
              grandtotalaftervat = +$("#vatTotallast").val() + total;
              $("#grandtotal").val(grandtotalaftervat);
              $("#grandtotaldue").val(grandtotalaftervat);
              $("#grandtotallast").val(grandtotalaftervat);
              $("#vatAmount").val(total);
          }else{
              grandtotalaftervat = +$("#vatTotallast").val() - total;
              $("#grandtotal").val(grandtotalaftervat);
              $("#grandtotaldue").val(grandtotalaftervat);
              $("#grandtotallast").val(grandtotalaftervat);
              $("#vatAmount").val(total);
          }
        }
      $(document).on( 'input', '.vat', function(){ calculateTotalDiscount(); } );
      //Entire Sale Vat calculet (end)

      function calculateTotalDiscount(){
        prodcalculate();
        allsalecalculate();
        allSaleVatCalculate();
      }

      $(document).ready(function () {
          calculateTotalDiscount();
          totalPay();
          calculateDueAndShowBtn();
      });
      

      //salse price new input calculet (start)
      function priceUpCalculate() {
        var total = 0;
        var n = $("input[name^='price']").length;
        var qty = $("input[name^='qty']");
        var upPrice = $("input[name^='price']");
        for(i=0; i<n; i++)
          {
            qt =  qty[i].value;
            up = upPrice[i].value;
            itemPrice = qt * up;
            total += itemPrice;
            $("#subtl_"+i).html(itemPrice.toFixed(2));
            $("#subt_"+i).val(itemPrice.toFixed(8));
            $("#subtl2_"+i).val(itemPrice.toFixed(8));
          }
          $("#totalamount").val(total.toFixed(8));
          $("#grandtotaldue").val(total.toFixed(8));
          $("#grandtotal").val(total.toFixed(8));
          $("#grandtotal2").val(total.toFixed(8));
          $("#grandtotallast").val(total.toFixed(8));
        }
      $(document).on( 'input', '.upprice', function(){ priceUpCalculate(); } );
      //salse price new input calculet (end)

      function priceMakeBase(val,key,printId){
          var total = val/key;
          $("#qtyUp_"+printId).val(total);
          priceUpCalculate();
      }


        function checkDueAmount(){
            var duetotal = $("#grandtotaldue").val();
            var name = $("#name").val();
            var customerId = $("#cus").val();

            //if (duetotal >= 0 && name != "" || duetotal >= 0 && customerId != "") {
            // if (duetotal < 0 || name == "" && duetotal < 0 || customerId == "") {
            if ((name == "" || duetotal < 0) && customerId == "") {
                $('#btn').hide();
                $('#mess').html('<span style="color:red">wrong input!! please correct inputs to proceed.</span>');
            }else{
                $('#btn').show();
                $('#mess').html('');
            }
        }



      //sale pay amount calculet (start)
      function totalPay(){
        var totalamount = 0;
        var total = $("#grandtotal").val();
        var nagod = $("#nagod").val();
        var bankAmount = $("#bankAmount").val();
        var chequeAmount = $("#chequeAmount").val();
        totalamount = ((total - nagod) - bankAmount) - chequeAmount;

        $("#grandtotaldue").val(totalamount);

          checkDueAmount();
      }
      $(document).on( 'input', '.nagod', function(){ totalPay(); } );
      $(document).on( 'input', '.bankAmount', function(){ totalPay(); } );
      $(document).on( 'input', '.chequeAmount', function(){ totalPay(); } );

      //sale pay amount calculet (end)

  function customerBalanceShow(id){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Sales/customerBalance') ?>",
          data: {customer_id:id},
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data){
              $("#loading-image").hide();
              $('#balance').html('Balance: ৳ '+ data.balance +'/-');
          }
      });
  }

  function emptyOponentCustomer() {
      const customerId = $('#cus').val();
      const customerName = $('#name').val();

      if (customerId) {
          $('#name').val('');
          $('#affiliate_user_id').val('').trigger('change');
      }

      if (customerName.trim() !== '') {
          if(customerId !== '') {
              $('#cus').val('').trigger('change');
          }
          $('#balance').html('');
      }
  }

    //all script sales from calculation needed (end)
</script>
<script type="text/javascript">

    //bank select check (start)
      function checkBankId(){
        var bankId = $('#bank_id').val();

          if (bankId == 0) {
            $('#Bank_valid').html('<span style="color:red">Please Select Bank</span>');
            $("#bankAmount").val('');
          }else{
            $('#Bank_valid').html('<span style="color:#238A09">Bank Selected </span>');
          }
      }
      //bank select check (end)


      //cheque number input check (start)
      function cheque(){
        var chequeNo = $('#chequeNo').val();
          if (chequeNo == 0) {
            $('#cheque_valid').html('<span style="color:red">Please input Cheque no..</span>');
            $("#chequeAmount").val('');
          }else{
            $('#cheque_valid').html('<span style="color:#238A09">Cheque no inputed </span>');
          }
      }
      //cheque number input check (start)

</script>
<script type="text/javascript">
  //all checkboxes check (start)
  function toggle(source) {
    checkboxes = document.getElementsByName('productId[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }
  //all checkboxes check (end)



//salse create page from valedation (Start)
$('#btn').hide();
var error = 0;
var customer_id = $('[name=customer_id]').val();
var name = $('[name=name]').val();

function createBtnShow(){
    checkDueAmount();
}

// function hideExtraInfo(){
//   var name = $('[name=name]').val();
//   var due = $('#grandtotaldue').val();
//   // if (name) {
//   //   $('[name=chequeNo]').attr("disabled", "disabled");
//   //   $('[name=chequeAmount]').attr("disabled", "disabled");
//   //   if (due > 0 ) {
//   //       error = 1;
//   //       $('#btn').hide();
//   //       $('#mess').html('<span style="color:red">Please clear due.</span>');
//   //   }else{
//   //     $('#btn').show();
//   //     $('#mess').html('');
//   //   }
//     checkDueAmount();
//   }


$(document).on( 'input', '#nagod', function(){ calculateDueAndShowBtn(); } );
$(document).on( 'input', '#bankAmount', function(){ calculateDueAndShowBtn(); } );
$(document).on( 'input', '#name', function(){ calculateDueAndShowBtn(); } );
function calculateDueAndShowBtn() {
    var totalPay = 0;
    var nagod = $('[name=nagod]').val();
    var bankAmount = $('[name=bankAmount]').val();
    totalPay = (+nagod) + (+bankAmount);
    var grandtotal = $('[name=grandtotal]').val();
    var name = $('[name=name]').val();
    if (name) {
      if (totalPay == grandtotal) {
        $('#btn').show();
        $('#mess').html('');
      }else{
        error = 1;
        $('#btn').hide();
        $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }

    }
}
//salse create page from valedation (End)

</script>

<script type="text/javascript">
  //employee  All Query (start)
  function employeeSearch(Id)
  {
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/search_employeeSalary') ?>",
        data: {id: Id},

        success: function(val){

          $("#salary").val(val).show();
        }
      });

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('transaction/ledger_employee') ?>",
        data: {id: Id},

        success: function(val){

          $("#ledger_employee").html(val).show();
        }
      });
  }

  function employeeLedg(Id)
  {
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_employee/search_ledger_employee') ?>",
          data: {id: Id},

          success: function(val){
            $("#ledger_employee").html(val).show();

            var name = $("#employeeId option:selected").text()

              $("#prName").html(name);
          }
        });
  }
  //employee  All Query (end)
</script>


<script type="text/javascript">
  //customer ledgher date to date search (start)
  function cusLedSerc()
  {
    var customerId = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (customerId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger/search_dateTodate') ?>",
          data: {customerId:customerId,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#customerLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function cusLedSercPrint()
  {
    var customerId = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger/search_dateTodatePrint') ?>",
          data: {customerId:customerId,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#ledgPrint").html(val).show();
          }
        });
  }
  //customer ledgher date to date search (end)
</script>

<script type="text/javascript">
  //supplier ledgher date to date search (start)
  function suppLedSerc()
  {
    var supplierId = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (supplierId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_suppliers/search_datetodate') ?>",
          data: {supplierId:supplierId,st_date:st_date,en_date:en_date},

          success: function(val){

            var target = ($('#supplierLedgerResult').length) ? $('#supplierLedgerResult') : $('#supplierLedg');
            target.html(val).show();
            syncSupplierLedgerPrint();

            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function suppLedSercprint(){
    var supplierId = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (supplierId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_suppliers/search_datetodatePrint') ?>",
          data: {supplierId:supplierId,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#ledgPrint").html(val).show();
          }
        });
    }
  }
  //supplier ledgher date to date search (end)
</script>

<script type="text/javascript">
  //bank ledgher date to date search (start)
  function bankLedSerc()
  {
    var bankId = $('#bankId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (bankId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_bank/search_dateTodate') ?>",
          data: {bankId:bankId,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#bankLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');
          }
        });
    }
  }

  function bankLedSercPrint()
  {
    var bankId = $('#bankId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_bank/search_dateTodatePrint') ?>",
          data: {bankId:bankId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
          $("#loading-image").hide();
            $("#ledgPrint").html(val).show();
          }
        });
  }
  //bank ledgher date to date search (end)
</script>

<script type="text/javascript">
  //loan Provider ledgher date to date search (start)
  function loProLedSerc()
  {
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (loanProId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_loan/search_dateTodate') ?>",
          data: {loanProId:loanProId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#lonProvLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function loProLedSercprint()
  {
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();


      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_loan/search_dateTodatePrint') ?>",
          data: {loanProId:loanProId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#ledgPrint").html(val).show();

          }
        });
  }
  //loan Provider ledgher date to date search (end)
</script>

<script type="text/javascript">
  //employee ledgher date to date search (start)
  function emplLedSerc()
  {
    var employeeId = $('#employeeId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");
    $('#prStD').html(st_date);
    $('#prEtD').html(en_date);


    if (employeeId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_employee/search_dateTodate') ?>",
          data: {employeeId:employeeId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#ledger_employee").html(val).show();
          }
        });
    }
  }

  function showHeader(){
      $('#printHead').css("display", "block");

  }
  //employee ledgher date to date search (end)
</script>

<script type="text/javascript">
  //Nagod / Cash ledger date to date filter (start)
  function nagodLedgerFilter(){
      var st_date = $('#st_date').val();
      var en_date = $('#en_date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_nagodan/search_dateTodate') ?>",
        data: {st_date: st_date, en_date: en_date},
        beforeSend: function(){
          if($.fn.DataTable.isDataTable('#example1')){
            $('#example1').DataTable().destroy();
          }
          var loader = '<div class="box"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div></div>';
          if($('#nagodLedgerResult').length){
            $("#nagodLedgerResult").html(loader);
          } else if($('#ledger_cash').length){
            $("#ledger_cash").html(loader);
          }
        },
        success: function(html){
          var target = ($('#nagodLedgerResult').length) ? $('#nagodLedgerResult') : $('#ledger_cash');
          target.html(html).show();
          syncNagodLedgerPrint();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          });
          ladgerTable.page('last').draw('page');
          updateNagodLedgerUrl();
        }
      });
  }

  function resetNagodLedgerFilter(){
    $('#st_date').val('');
    $('#en_date').val('');
    nagodLedgerFilter();
  }

  // move the printable rows of the filtered result into #ledgPrint (Print / PDF / CSV)
  function syncNagodLedgerPrint(){
    var printHtml = $('#ajaxNagodLedgPrint').html();
    if(printHtml !== undefined && printHtml !== null && printHtml.trim() !== ''){
      $('#ledgPrint').html(printHtml);
      $('#ajaxNagodLedgPrint').remove();
    }
  }

  // keep the browser url in sync with the current filter (so reload/print keeps it)
  function updateNagodLedgerUrl(){
    if(!window.history || !window.history.replaceState){ return; }
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var url = '<?php echo site_url('Admin/Ledger_nagodan') ?>';
    var params = [];
    if(st_date){ params.push('st_date=' + encodeURIComponent(st_date)); }
    if(en_date){ params.push('en_date=' + encodeURIComponent(en_date)); }
    if(params.length){ url = url + '?' + params.join('&'); }
    window.history.replaceState(null, '', url);
  }

  // Aliases for convenience / backward compatibility
  var cashLedgerFilter = nagodLedgerFilter;
  var resetCashLedgerFilter = resetNagodLedgerFilter;
  var syncCashLedgerPrint = syncNagodLedgerPrint;
  var updateCashLedgerUrl = updateNagodLedgerUrl;

  function nagodLedSerc()
  {
    nagodLedgerFilter();
  }

  function nagodLedSercPrint()
  {
    syncNagodLedgerPrint();
  }
  //Nagod / Cash ledger date to date filter (end)
</script>

<script type="text/javascript">
  function storePro(storeId)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Stock_report/search_store_product') ?>",
      data: {storeId:storeId},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(val){
        $("#loading-image").hide();
        $("#product").html(val).show();
        $("#prentView").html(val).show();
      }
    });
  }
</script>


<script type="text/javascript">

  function vatLedgerView(vatId)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/vatLedgerData') ?>",
      data: {vatId:vatId},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(val){
        $("#loading-image").hide();
        $("#vatledger").html(val).show();
      }
    });
  }



  function checkAvailableBankAmount(amount)
  {
    var bankId = $('#bank_id').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/check_bank_balance') ?>",
        dataType: "text",
        data: {balance: amount,bank_id: bankId},

        beforeSend: function() {
          $("#loading-image").show();
        },
        success: function(msg){
          $("#loading-image").hide();
            $('#Bank_valid').html(msg);

        }
      });
  }

</script>


<script type="text/javascript">
  //return sale form validetion (start)
  function hideExtraInfoReturn(){
    var name = $('[name=name]').val();
    var customerId = $('[name=customer_id]').val();
    var due = $('#grandtotaldue').val();
    //alert(customerId);
    if (name || customerId) {
      $('[name=chequeNo]').attr("disabled", "disabled");
      $('[name=chequeAmount]').attr("disabled", "disabled");
      if (due > 0 ) {
          error = 1;
          $('#btn').hide();
          $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }else{
        $('#btn').show();
        $('#mess').html('');
      }
    }
  }

  $(document).on( 'input', '.nagodpay', function(){ calculateDueAndReturnSaleShowBtn(); } );
  $(document).on( 'input', '.bankAmountpay', function(){ calculateDueAndReturnSaleShowBtn(); } );

  function calculateDueAndReturnSaleShowBtn() {
    var totalPay = 0;
    var nagod = $('[name=nagod]').val();
    var bankAmount = $('[name=bankAmount]').val();
    totalPay = (+nagod) + (+bankAmount);
    var grandtotal = $('[name=grandtotal]').val();
    var name = $('[name=name]').val();
    var customerId = $('[name=customer_id]').val();
    if (name || customerId) {
      if (totalPay >= grandtotal) {
        $('#btn').show();
        $('#mess').html('');
      }else{
        error = 1;
        $('#btn').hide();
        $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }
    }
  }

  //return sale form validetion (end)





  // These script is for product purchase Exixting Itame -- Start ---
        function check_pro_qty() {

          var n = $("input.datatables").length;

          var newqnt = $("input[name^='quantity']");
          var oldqnt = $("input[name^='qty']");

          var oldquantity = 0;
          var newquantity = 0;
          for(i=0;i<n;i++)
          {
            if($('input.datatables')[i].checked) {
                  oldquantity =  oldqnt[i].value;
                  newquantity =  newqnt[i].value;

                  if (oldquantity >= newquantity ) {

                    $("#quantity").css("border-color", "#ecf0f5");
                  }else{

                    $("#quantity").css("border-color", "#f90808");
                  }
            }
          }

        }

        $(document).on( 'input', '.datatables', function(){ check_pro_qty(); } );
        $(document).on( 'input', '.quantity', function(){ check_pro_qty(); } );

</script>

<script type="text/javascript">
  function getCustomer(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('lc_transaction/lcCustomer') ?>",
        dataType: "text",
        data: {Id:id},

      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(msg){
        $("#loading-image").hide();
          $('#lccustomer').html(msg);
      }
    });
  }


  function getSupplier(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('lc_transaction/lcSupplier') ?>",
        dataType: "text",
        data: {Id:id},

      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(msg){
        $("#loading-image").hide();
          $('#supplierId').html(msg);
      }
    });
  }

  function lcLedgDebit(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Debit') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
          // $("#print").css('display','none');
      }
    });
  }

  function lcLedgDebitPrint(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Debitprint') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedgprint").html(html).show();
          // $("#print").css('display','none');
      }
    });
  }

  function lcLedgCredit(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Credit') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
      }
    });
  }

  function lcLedgCreditPrint(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_CreditPrint') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedgprint").html(html).show();
      }
    });
  }



</script>

<!-- print -->
<script type="text/javascript">

  function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       document.body.innerHTML = printContents;
       var printed = false;
       var doPrint = function(){
         if(printed){ return; }
         printed = true;
         window.print();
         location.reload();
       };
       // wait for the images (logo) to load, otherwise they are missing on the printout
       var imgs = Array.prototype.slice.call(document.images || []);
       var pending = imgs.filter(function(img){ return !img.complete; });
       if(pending.length === 0){
         doPrint();
         return;
       }
       var loaded = 0;
       var onReady = function(){
         loaded++;
         if(loaded >= pending.length){ doPrint(); }
       };
       pending.forEach(function(img){
         img.addEventListener('load', onReady);
         img.addEventListener('error', onReady);
       });
       setTimeout(doPrint, 3000);
  }

  function showData(url,getUrl2){

    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $("#viewpage").html(html).show();
        $("#viewpage").css("margin-left","0px");
        $('.select2').select2();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
        });
        ladgerTable.page('last').draw('page');

          $('#customer2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#supplier2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#account2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#fund2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#expense2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#other2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#employee2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#vat2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#assets1').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });

        yearpicker();
        history.pushState({ foo: 'bar' }, '', '<?php echo base_url() ?>'+getUrl2);
        if (getUrl2) {
          $('.sidebar-menu a').each(function() {
            var fn = $(this).attr('onclick') || '';
            if (fn.indexOf("'" + getUrl2 + "'") !== -1 || fn.indexOf('"' + getUrl2 + '"') !== -1) {
              $('.treeview-menu li').removeClass('active');
              $('ul .active').removeClass('active');
              $('.treeview .menu-open').removeClass('active menu-open');
              $(this).parent().addClass('active');
              $(this).closest('.treeview').addClass('active menu-open');
            }
          });
        }
      }
    });
  }

  function activeTab(val){
      $('.treeview-menu li').attr('class', '');
      $('ul .active').attr('class', '');
      $('.treeview .menu-open').attr('class', '');
      $(val).parent().attr('class','active');
  }

  $(document).on('submit','#geniusform',function(e){
    e.preventDefault();

    $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
    var fd = new FormData(this);

    var geniusform = $(this);
    $('button.geniusSubmit-btn').prop('disabled',true);
    $.ajax({
      method:"POST",
      url:$(this).prop('action'),
      data:fd,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function() {
        $("#loading-image").show();
      },
      success:function(data){
        $("#loading-image").hide();
          $('#message').hide();
          $('#message').show();
          $('#message').html(data);
          $('#geniusform')[0].reset();
          $('#geniusform select.select2').val('').trigger('change');
          $.ajax({
            method:"get",
            url:"<?php echo site_url('Admin/Transaction/updated_case') ?>",
            success:function(cash){
                $('#reloaddiv').html(cash);
            }
          });
          $(".input").val("");
          $("#subCat").val("");
          $("#custData").html("");
          $("#suppData").html("");
          $("#lonProvData").html("");
          $("#bankData").html("");
          $("#ledger_employee").html("");
          $("#vatledger").html("");
          $(".viewpage").html(data);
          $('#reload').load(document.URL + ' #reload');
          $('#reloadimg').load(document.URL + ' #reloadimg');
        $('button.geniusSubmit-btn').prop('disabled',false);
        $(window).scrollTop(0);
      }

    });

  });

  function customerTypeValidat(){
      var typeName = $('#type_name').val();

      if (required(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Type name field cannot be empty</div>');
      }else if(numericOrStringCheck(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#type_name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var type_name_validation = true;
      }

      if (type_name_validation == true){
          $('#geniusform').submit();
      }

  }

  function customerValidat(){
      var name = $('#customer_name').val();
      var mobile = $('#mobile').val();
      var cus_type = $('#cus_type_id').val();
      var affiliate_user_id = $('#affiliate_user_id').val();

      if (required(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer name field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#customer_name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var customer_name_validation = true;
      }

      if (required(mobile) == false){
          $('#mobile').parent().find('.error').html('<div style="color:red;" id="mesWrong">Mobile field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#mobile').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#mobile').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(cus_type) == false){
          $('#cus_type_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer Type field cannot be empty</div>');
      }else{
          $('#cus_type_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var cus_type_validation = true;
      }

      if (required(affiliate_user_id) == false){
          $('#affiliate_user_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">Affiliate user field cannot be empty</div>');
      }else{
          $('#affiliate_user_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var cus_type_validation = true;
      }

      if ((customer_name_validation == true) && (phone_validation == true) &&(cus_type_validation == true) ){
          $('#geniusform').submit();
          $('#customerform').submit();
      }
  }

  function customerExisValidat(){
      var name = $('#customer_name_ex').val();
      var mobile = $('#mobile_ex').val();
      var cus_type = $('#cus_type_id_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer name field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#customer_name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var customer_name_validation = true;
      }

      if (required(mobile) == false){
          $('#mobile_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Mobile field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#mobile_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#mobile_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(cus_type) == false){
          $('#cus_type_id_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer Type field cannot be empty</div>');
      }else{
          $('#cus_type_id_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var cus_type_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">Transaction Type field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Amount Type field cannot be empty</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((customer_name_validation == true) && (phone_validation == true) && (cus_type_validation == true) && (transaction_type_validation == true) && (amount_validation == true) ){
          $('#geniusform3').submit();
          $('#customerform2').submit();
      }
  }

  function bankValidat(){
      var name = $('#name').val();
      var accountName = $('#account_name').val();
      var account_no = $('#account_no').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(accountName) == false){
          $('#account_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(accountName) == false){
          $('#account_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(accountName) == false){
          $('#account_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#account_name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var accountName_validation = true;
      }

      if (required(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else if (lengthValidation(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#account_no').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var account_no_validation = true;
      }

      if ((name_validation == true) && (account_no_validation == true) && (accountName_validation == true)){
          $('#geniusform').submit();
      }
  }

  function bankExValidat(){
      var name = $('#name_ex').val();
      var accountName = $('#account_name_ex').val();
      var account_no = $('#account_no_ex').val();
      var amount_ex = $('#amount_ex').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(accountName) == false){
          $('#account_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(accountName) == false){
          $('#account_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(accountName) == false){
          $('#account_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          var accountName_validation = true;
      }

      if (required(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else if (lengthValidation(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#account_no_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var account_no_validation = true;
      }

      if (required(amount_ex) == false){
          $('#amount_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount_ex) == false){
          $('#amount_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#amount_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_ex_validation = true;
      }


      if ((name_validation == true) && (account_no_validation == true) && (amount_ex_validation == true) && (accountName_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function bankDepositValidat() {
      var bank = $('#bank_id').val();
      var amount = $('#amount').val();

      if (required(bank) == false){
          $('#bank_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          // $('#bank_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var bank_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }


      if ((bank_validation == true) && (amount_validation == true)){
          $('#geniusform').submit();
      }
  }

  function brandValidat() {
      var name = $('#name').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function proCatValidat() {
      var name = $('#product_category').val();

      if (required(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#product_category').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function storeValidat() {
      var name = $('#name').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function suppliersValidat(){
      var name = $('#name').val();
      var mobile = $('#phone').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#geniusform').submit();
          $('#supplierform').submit();
      }
  }

  function suppliersExValidat(){
      var name = $('#name_ex').val();
      var mobile = $('#phone_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true) && (transaction_type_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
          $('#supplierform2').submit();
      }
  }

  function accountValidat() {
      var name = $('#name').val();
      var mobile = $('#phone').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#geniusform').submit();
      }
  }

  function accountExValidat(){
      var name = $('#name_ex').val();
      var mobile = $('#phone_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true) && (transaction_type_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function employeeValidat() {
      var name = $('#name').val();
      var salary = $('#salary').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(salary) == false){
          $('#salary').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(salary) == false){
          $('#salary').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#salary').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var salary_validation = true;
      }

      if ((name_validation == true) && (salary_validation == true)){
          $('#geniusform').submit();
      }
  }

  function employeeExValidat() {
      var name = $('#name_ex').val();
      var salary = $('#salary_ex').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(salary) == false){
          $('#salary_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(salary) == false){
          $('#salary_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#salary_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var salary_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (salary_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function userValidat(){
      var name = $('#name').val();
      var email = $('#email').val();
      var password = $('#password').val();
      var con_password = $('#con_password').val();
      var role_id = $('#role_id').val();
      var status = $('#status').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(password) == false){
          $('#password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var password_validation = true;
      }

      if (required(con_password) == false){
          $('#con_password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#con_password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var con_password_validation = true;
      }

      if (required(role_id) == false){
          $('#role_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#role_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var role_id_validation = true;
      }

      if (required(status) == false){
          $('#status').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#status').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var status_validation = true;
      }

      if (required(email) == false){
          $('#email').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(emailValidation(email) == false){
          $('#email').parent().find('.error').html('<div style="color:red;" id="mesWrong">Enter a valid email address</div>');
      }else{
          $('#email').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var email_validation = true;
      }

      if ((name_validation == true) && (password_validation == true) && (con_password_validation == true) && (role_id_validation == true) && (status_validation == true) && (email_validation == true) ){
          $('#geniusform').submit();
      }


  }




  function numericOrStringCheck(val){
      if ($.isNumeric(val) == false) {
          return true;
      }else {
          return false;
      };
  }
  function required(val){
      if (val == ""){
          return false;
      }else{
          return true;
      }
  }
  function lengthValidation(val){
      var length = val.length;
      if (length <= 32){
          return true;
      }else{
          return false;
      }
  }
  function notNumericCheck(val){
      if ($.isNumeric(val) == true) {
          return true;
      }else {
          return false;
      };
  }

  function emailValidation(val){
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      // return emailReg.test(val);
      if (!emailReg.test(val)) {
          return false;
      }else {
          return true;
      };
  }

  function not0(val){
      if (val == 0){
          return false;
      }else{
          return true;
      }
  }
  function emojiCheck(val){
      // if (val == 0){
      //     return false;
      // }else{
      //     return true;
      // }
  }



  $(document).on('submit','#geniusform3',function(e){
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var geniusform = $(this);
      $('button.geniusSubmit-btn3').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);
              $('#geniusform3')[0].reset();
              $.ajax({
                  method:"get",
                  url:"<?php echo site_url('transaction/updated_case') ?>",
                  success:function(cash){
                      $('#reloaddiv').html(cash);
                  }
              });
              $(".input").val("");
              $("#custData").html("");
              $("#suppData").html("");
              $("#lonProvData").html("");
              $("#bankData").html("");
              $("#ledger_employee").html("");
              $("#vatledger").html("");
              $(".viewpage").html(data);
              $('#reload').load(document.URL + ' #reload');
              $('#reloadimg').load(document.URL + ' #reloadimg');
              $('button.geniusSubmit-btn3').prop('disabled',false);
              $(window).scrollTop(0);
          }

      });

  });

  $(document).on('submit','#geniusformUpdate',function(e){
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var geniusform = $(this);
      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);
              $.ajax({
                  method:"get",
                  url:"<?php echo site_url('Admin/Transaction/updated_case') ?>",
                  success:function(cash){
                      $('#reloaddiv').html(cash);
                  }
              });
              $(".input").val("");
              $("#custData").html("");
              $("#suppData").html("");
              $("#lonProvData").html("");
              $("#bankData").html("");
              $("#ledger_employee").html("");
              $("#vatledger").html("");
              $(".viewpage").html(data);
              $('#reload').load(document.URL + ' #reload');
              $('#reloadimg').load(document.URL + ' #reloadimg');
              $('button.geniusSubmit-btn').prop('disabled',false);
              $(window).scrollTop(0);
          }

      });

  });


  function _delete(url,getUrl){
      var confirmed = confirm('Are You Sure ?');
      if (confirmed) {
          $.ajax({
              type: "GET",
              url: url,
              data: {},
              beforeSend: function () {
                  $("#loading-image").show();
              },
              success: function (mess) {
                  $("#loading-image").hide();
                  $('#message').html(mess);
                  $('#reload').load(document.URL + '#reload table');
              }
          });
      }
  }

  // starting_closing module (start)

  $(document).on('submit','#geniusform2',function(e){
    e.preventDefault();

    var fd = new FormData(this);
    var geniusform2 = $(this);

    if (confirm("your previous data can be destroyed! Are you sure?")) {
      $('button.geniusSubmit-btn2').prop('disabled',true);
      $.ajax({
        method:"POST",
        url:$(this).prop('action'),
        data:fd,
        contentType: false,
        cache: false,
        processData: false,
        success:function(data){
            $('#message').hide();
            $('#message').show();
            $('#message').html(data);
            $('#geniusform2')[0].reset();
            //$.ajax({
            //  method:"get",
            //  beforeSend: function() {
            //    $("#loading-image").show();
            //  },
            //  url:"<?php //echo site_url('transaction/updated_case') ?>//",
            //  success:function(cash){
            //    $("#loading-image").hide();
            //      $('#reloaddiv').html(cash);
            //  }
            //});

          $('button.geniusSubmit-btn2').prop('disabled',false);
          $(window).scrollTop(0);
        }

      });
    }

  });
   // starting_closing module (end)



// yearly_closing module (start)
  function closingData(url){
     if(confirm("your previous data can be destroyed! Are you sure?")){
        $.ajax({
          type: "POST",
          url: url,
          data: {},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(html){
            $("#loading-image").hide();
            $('#message').html(html);
          }
        });
    }
  }
// yearly_closing module (end)

//shop_opening_status change(start)
function opening_status(url){
     if(confirm("Are you confirming to run the business?")){
        $.ajax({
          type: "POST",
          url: url,
          data: {},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(html){
            $("#loading-image").hide();
            $('#message').html(html);
            setTimeout(function(){
                location.reload();
            }, 1000);
          }
        });
    }
  }
//shop_opening_status change(end)

  // 'previus data show' modules script (start)
  function yearpicker(){
    let startYear = 2021;
    let endYear = new Date().getFullYear();
    for (i = endYear; i > startYear; i--)
    {
      $('#yearpicker').append($('<option></option>').val(i).html(i));
    }
  }

  $(document).ready(
    yearpicker()
  );


  function url_ledger(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#hedden").show();
        $("#loading-image").hide();
        $('#all_user').html(html);
        $('#nameLabel').html('');
        $('.select2').select2();
        // alert(html);
      }
    });
  }

  function searchLedger(id,url){
    $.ajax({
      type: "POST",
      url: url,
      data: {Id:id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#nameLabel').html(html);
      }
    });
  }

  function searchLedgercash(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $("#hedden").hide();
        $('#nameLabel').html(html);
      }
    });
  }

  function reportView(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        // $("#hedden").hide();
        $('#reportView').html(html);
      }
    });
  }

  function reportData(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#reportView').html(html);
        $('.select2').select2();
      }
    });
  }

  function searchReport(id,url){
    $.ajax({
      type: "POST",
      url: url,
      data: {Id:id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#reportResult').html(html);
      }
    });
  }
  // 'previus data show' modules script (end)

  function QrScan(id){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Sales/scanAddToCart') ?>",
          data: {prod_id:id},
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(message){
              $("#loading-image").hide();
              $("#message").html(message);
              $("#TFtable").load(location.href + " #TFtable");
              $('#qrKey').val('');

          }
      });
  }



  function minusValueCheck(val,identity){
      if ( val < 0 ){
          $(identity).val('');
          $(identity).parent().append('<div style="color:red;" id="mesWrong">Wrong Input</div>');
      }else{
          $('#mesWrong').css('color','green');
          $('#mesWrong').html('Success');
      }
  }

  function addtocartService(){
      var service_name = $('[name=service_name]').val();
      var price = $('[name=price]').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Service/addCart') ?>",
          dataType: "text",
          data: {service_name: service_name,price:price},
          success: function(msg){
              $('#message').html(msg);
              location.reload();
          }
      });
  }

  function generateBtnShow(){
      var duetotal = $("#grandtotaldue").val();
      var serviceType = $("#service_type").val();
      var title = $("#title").val();
      var name = $("#nameId").val();
      var customerId = $("#cus").val();

      var error = 0;
      if (customerId == "" && name == "" ) { error = 1; }
      if (serviceType == "" ) { error = 1; }
      if (title == "" ) { error = 1; }
      if (duetotal < 0 ) { error = 1; }
      if (error == 1 ) {
          $('#btn').hide();
          $('#mess').html('<span style="color:red">wrong input!! please correct inputs to proceed.</span>');
      }else{
          $('#btn').show();
          $('#mess').html('');
      }
  }

  $(document).on('submit','#supplierform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var supplierform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#message2').html(data.message);
              if(data.success == true){
                  $('#supplierform')[0].reset();
                  $('#supData').html(data.htmlData);
                  $('#supplier_id').html(data.htmlData);
                  setTimeout(function() {
                      $('#message2').fadeOut();
                      $('#modal-default').modal('hide');
                  }, 2000);
              }
          }
      });

  });

  $('#modal-customer').on('hidden.bs.modal', function () {
      $('#cusmessage2').empty();
      $('.error').empty();
  });

  $('#modal-default').on('hidden.bs.modal', function () {
      $('#message2').empty();
      $('.error').empty();
  });

  $('#modal-account').on('hidden.bs.modal', function () {
      $('#messageAcc').empty();
      $('.error').empty();
  });

  $(document).on('submit','#customerform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var customerform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#cusmessage2').html(data.message);

              if(data.success == true){
                  $('#customerform')[0].reset();
                  $('#cusData').html(data.htmlData);
                  $('#cus').html(data.htmlData);
                  setTimeout(function() {
                      $('#message').fadeOut();
                      $('#modal-customer').modal('hide');
                  }, 2000);

              }
          }
      });

  });

  function accountHeadValidat() {
      var name = $('#nameAcc').val();
      var mobile = $('#phoneAcc').val();

      if (required(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#nameAcc').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phoneAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phoneAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phoneAcc').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#accountform').submit();
      }
  }

  $(document).on('submit','#accountform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var accountform = $(this);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#messageAcc').html(data.message);

              if(data.success == true){
                  $('#accountform')[0].reset();
                  $('#loanData').html(data.htmlData);
                  setTimeout(function() {
                      $('#messageAcc').fadeOut();
                      $('#modal-account').modal('hide');
                  }, 2000);
              }
          }
      });

  });


  $(document).on('submit','#cusUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var cusUpdateform = $(this);
      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#cusUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#customer2').load(document.URL + ' #customer2');

          }

      });

  });

  $(document).on('submit','#suppUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#suppUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#supplier2').load(document.URL + ' #supplier2');
          }

      });

  });

  $(document).on('submit','#accountUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#accountUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#account2').load(document.URL + ' #account2');
          }

      });

  });

  $(document).on('submit','#otherSalesUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#otherSalesUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#other2').load(document.URL + ' #other2');
          }

      });

  });

  $(document).on('submit','#employeeUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#employeeUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#employee2').load(document.URL + ' #employee2');
          }

      });

  });

  $(document).on('submit','#expenseUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#expenseUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#expense2').load(document.URL + ' #expense2');
          }

      });

  });

  $(document).on('submit','#vatUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#vatUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#vat2').load(document.URL + ' #vat2');
          }

      });

  });

  $(document).on('submit','#fundUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#fundUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#fund2').load(document.URL + ' #fund2');
          }

      });

  });

  $(document).on('submit','#depositUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#depositUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#example1').load(document.URL + ' #example1');
          }

      });

  });

  $(document).on('submit','#withdrawUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#withdrawUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#example1').load(document.URL + ' #example1');
              $('#bankData').load(document.URL + ' #bankData');
          }

      });

  });

  $(document).on('submit','#customerform2',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var customerform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#cusmessage2').html(data.message);

              if(data.success == true){
                  $('#customerform2')[0].reset();
                  $('#cusData').html(data.htmlData);
                  $('#cus').html(data.htmlData);
                  $('#reload').load(document.URL + ' #reload');
                  setTimeout(function() {
                      $('#message').fadeOut();
                      $('#modal-customer').modal('hide');
                  }, 2000);

              }
          }
      });

  });

  $(document).on('submit','#supplierform2',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var supplierform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#message2').html(data.message);
              if(data.success == true){
                  $('#supplierform2')[0].reset();
                  $('#supData').html(data.htmlData);
                  $('#supplier_id').html(data.htmlData);
                  setTimeout(function() {
                      $('#message2').fadeOut();
                      $('#modal-default').modal('hide');
                  }, 2000);
              }
          }
      });

  });

  function typeChange(value) {
      const config = {
          product:  { div: "productDiv",  input: "prodId" },
          brand:    { div: "brandDiv",    input: "brandId" },
          category: { div: "categoryDiv", input: "prodCatId" }
      };

      // Reset everything
      Object.values(config).forEach(item => {
          document.getElementById(item.div).style.display = "none";
          document.getElementById(item.input).required = false;
      });

      // Activate selected section
      if (config[value]) {
          document.getElementById(config[value].div).style.display = "block";
          document.getElementById(config[value].input).required = true;
      }
  }
  

  function affiliateUserValidat(){
      var name = $('#name').val();
      var phone = $('#phone').val();
      var commission = $('#commission').val();
      var address = $('#address').val();
      var password = $('#password').val();
      var con_password = $('#con_password').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Maximum length of 32 characters</div>');
      }else{
          var name_validation = true;
      }

      if (required(phone) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(phone) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          var phone_validation = true;
      }

      if (required(commission) == false){
          $('#commission').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(commission) == false){
          $('#commission').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          var commission_validation = true;
      }

      if (required(address) == false){
          $('#address').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          var address_validation = true;
      }

      if (required(password) == false){
          $('#password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          var password_validation = true;
      }

      if (required(con_password) == false){
          $('#con_password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          var con_password_validation = true;
      }

      if ((name_validation == true) && (commission_validation == true) && (address_validation == true) && (phone_validation == true) && (password_validation == true) && (con_password_validation == true)){
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#commission').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#address').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          $('#con_password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');

          $('#geniusform').submit();
      }
  }

  function downloadPDF(dataId,prefix) {
      // Show hidden div before generating PDF
      let element = document.getElementById(dataId);
      element.style.display = 'block';
      let opt = {
          margin: [10, 0, 0, 0], // top, left, bottom, right
          filename: 'ledger-'+prefix+'.pdf',
          image: { type: 'jpeg', quality: 1 },
          html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait'  },
          pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };

      html2pdf().set(opt).from(element).save().then(() => {
          // Hide again after download
          element.style.display = 'none';
      });
  }

  function downloadCSV(dataId,prefix) {
      let id = '#'+dataId;
      let table = document.querySelector(id+" table");
      let rows = table.querySelectorAll("tr");
      let csv = [];
      rows.forEach(row => {
          let cols = row.querySelectorAll("th, td");
          let rowData = [];
          cols.forEach(col => {
              let text = col.innerText
                  .replace(/(\r\n|\n|\r)/gm, "")
                  .replace(/,/g, " "); // remove commas
              rowData.push('"' + text + '"');
          });
          csv.push(rowData.join(","));
      });

      // Create CSV file
      let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

      // Download link
      let downloadLink = document.createElement("a");
      downloadLink.download = "ledger-"+prefix+".csv";
      downloadLink.href = window.URL.createObjectURL(csvFile);

      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
  }
  function formSubmit(){
      $('form').submit();
  }

  function checkedFunction(targetModule, button) {
      // Find all checkboxes belonging to this module array group
      const checkboxes = document.querySelectorAll(`input[type="checkbox"][name^="permission[${targetModule}]"]`);

      // Check if they are already all checked
      const allChecked = Array.from(checkboxes).every(cb => cb.checked);

      // If all are checked, uncheck them. Otherwise, check them all.
      checkboxes.forEach(cb => {
          cb.checked = !allChecked;
      });

      // Toggle button text visually using the passed button reference
      button.textContent = allChecked ? 'Checked' : 'Unchecked';
  }
  function selectAll(button){
      // Find all checkboxes belonging to this module array group
      const checkboxes = document.querySelectorAll(`input[type="checkbox"]`);

      // Check if they are already all checked
      const allChecked = Array.from(checkboxes).every(cb => cb.checked);

      // If all are checked, uncheck them. Otherwise, check them all.
      checkboxes.forEach(cb => {
          cb.checked = !allChecked;
      });

      // Optional: Toggle button text visually
      button.textContent = allChecked ? 'Select All' : 'Unselect All';
  }

  function toggleAllCategories(button) {
      const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(:disabled)');

      const allChecked = Array.from(checkboxes).every(cb => cb.checked);

      checkboxes.forEach(cb => {
          cb.checked = !allChecked;
      });

      button.textContent = allChecked ? 'Select All' : 'Unselect All';
  }

  function rollPermissionBtn() {
      $('#permissionDiv').fadeToggle(500);
  }

  function rolePermission(role_id,module){
      $.ajax({
          method: "POST",
          url: '<?= base_url("Admin/Role/modulePermission")?>',
          data: {role_id:role_id,module:module},
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data) {
              $("#loading-image").hide();
              $('#rolView').html(data);
          }
      });
  }

  $(document).on('submit', '#roleUpdateform', function(e) {
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var roleUpdateform = $(this);

      $.ajax({
          method: "POST",
          url: $(this).prop('action'),
          data: fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data) {
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);

              $('#reloadRoleDiv').load(document.URL + ' #reloadRoleDiv');
          }

      });

  });

  $(document).on('submit', '#assetsUpdateform', function(e) {
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled', true);
      $.ajax({
          method: "POST",
          url: $(this).prop('action'),
          data: fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data) {
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#assetsUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#assets1').load(document.URL + ' #assets1');
              $('#expense2').load(document.URL + ' #expense2');
              $('#other2').load(document.URL + ' #other2');
          }

      });

  });

  function unitSetValidat() {
      var name = $('#name').val();
      var unit_categories_id = $('#unit_categories_id').val();

      if (required(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      } else if (numericOrStringCheck(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      } else if (lengthValidation(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      } else {
          var name_validation = true;
      }
      if (required(unit_categories_id) == false || unit_categories_id == "") {
          $('#unit_categories_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      } else {
          var unit_categories_id_validation = true;
      }


      if ((name_validation == true) && (unit_categories_id_validation == true)) {
          $('#geniusform').submit();
      }
  }

  function unitShowInOption(categories_id) {

      $.ajax({
          method: "POST",
          url: '<?= base_url("Admin/Unit_set/unitShowInOption")?>',
          data: { unit_categories_id: categories_id },
          dataType: 'json',
          success: function(data) {
              var purchaseCheckboxes = '';
              var purchasePriceCheckboxes = '';
              var sellCheckboxes = '';
              var sellPriceCheckboxes = '';

              if (data && data.length > 0) {
                  $.each(data, function(index, unit) {
                      // Build checkbox markup for Purchase Units
                      purchaseCheckboxes += '  <label style="margin-left: 10px;"><input type="checkbox" name="purchase_units[]" value="' + unit.units_id + '"> ' + unit.name + '</label>';
                      // Build checkbox markup for Purchase price Units
                      purchasePriceCheckboxes += '  <label style="margin-left: 10px;"><input type="radio" name="purchase_price" value="' + unit.units_id + '"> ' + unit.name + '</label>';
                      // Build checkbox markup for Sale Units
                      sellCheckboxes += '  <label style="margin-left: 10px;"><input type="checkbox" name="sell_units[]" value="' + unit.units_id + '"> ' + unit.name + '</label>';
                      // Build checkbox markup for Sale price Units
                      sellPriceCheckboxes += '  <label style="margin-left: 10px;"><input type="radio" name="sell_price" value="' + unit.units_id + '"> ' + unit.name + '</label>';
                  });
              } else {
                  purchaseCheckboxes = '<p class="text-muted">No units found for this category.</p>';
                  purchasePriceCheckboxes = '<p class="text-muted">No units found for this category.</p>';
                  sellCheckboxes = '<p class="text-muted">No units found for this category.</p>';
                  sellPriceCheckboxes = '<p class="text-muted">No units found for this category.</p>';
              }

              // Inject the generated checkboxes into their respective container DIVs
              $('#purchase_units_container').html(purchaseCheckboxes);
              $('#purchase_price_units_container').html(purchasePriceCheckboxes);
              $('#sell_units_container').html(sellCheckboxes);
              $('#sell_price_units_container').html(sellPriceCheckboxes);
          },
          error: function(xhr, status, error) {
              console.error("Error fetching units: ", error);
          }
      });

  }


  function unitShow(categories_id){
      if (categories_id) {
          var noPurchase = $('#noPurchase').val();
          $.ajax({
              method: "POST",
              url: '<?= base_url("Admin/Purchase/unitShow")?>',
              data: {unit_set_id: categories_id,noPurchase:noPurchase},
              beforeSend: function () {
                  $("#loading-image").show();
              },
              success: function (data) {
                  $("#loading-image").hide();
                  // alert(data);
                  $('#unitData').html(data);
              }

          });
      }else{
          $('#unitData').html('');
      }
  }

  $(function() {
      var categories_id = $('#categories_id').val();
      if (categories_id) {
          var noPurchase = $('#noPurchase').val();
          $.ajax({
              method: "POST",
              url: '<?= base_url("Admin/Purchase/unitShow")?>',
              data: {unit_set_id: categories_id,noPurchase:noPurchase},
              beforeSend: function () {
                  $("#loading-image").show();
              },
              success: function (data) {
                  $("#loading-image").hide();
                  // alert(data);
                  $('#unitData').html(data);
              }

          });
      }
  });

  $(document).on('submit', '#adtoCartform', function(e) {
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var geniusform = $(this);
      $('button.geniusSubmit-btn').prop('disabled', true);
      $.ajax({
          method: "POST",
          url: $(this).prop('action'),
          data: fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data) {
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);
              $('#adtoCartform')[0].reset();
              location.reload();
          }

      });

  });

  $(document).on('submit', '#typeform', function(e) {
      e.preventDefault();

      var fd = new FormData(this);

      var typeform = $(this);

      $.ajax({
          method: "POST",
          url: $(this).prop('action'),
          data: fd,
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data) {
              $("#loading-image").hide();
              $("#message").hide();
              $('#message').html(data.message);

              if (data.success == true) {
                  $('#typeform')[0].reset();
                  $('#sub_type').html(data.htmlData);
                  $('#sub_type2').html(data.htmlData);
                  setTimeout(function() {
                      $('#message').fadeOut();
                      $('#modal-create').modal('hide');
                  }, 2000);

              }
          }
      });

  });

  function unitCategoryValidat() {
      var name = $('#name').val();

      if (required(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      } else if (numericOrStringCheck(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      } else if (lengthValidation(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      } else {
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true) {
          $('#geniusform').submit();
      }
  }

  function unitValidat() {
      var name = $('#name').val();
      var symbol = $('#symbol').val();
      var unit_categories_id = $('#unit_categories_id').val();
      var conversion_factor = $('#conversion_factor').val();
      var decimal_places = $('#decimal_places').val();

      if (required(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      } else if (numericOrStringCheck(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      } else if (lengthValidation(name) == false) {
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      } else {
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(symbol) == false) {
          $('#symbol').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else {
          var symbol_validation = true;
      }
      if (required(unit_categories_id) == false || unit_categories_id == "") {
          $('#unit_categories_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      } else {
          var unit_categories_id_validation = true;
      }
      if (required(conversion_factor) == false) {
          $('#conversion_factor').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else {
          var conversion_factor_validation = true;
      }
      if (required(decimal_places) == false) {
          $('#decimal_places').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else {
          var decimal_places_validation = true;
      }


      if ((name_validation == true) && (symbol_validation == true) && (unit_categories_id_validation == true) && (conversion_factor_validation == true) && (decimal_places_validation == true)) {
          $('#geniusform').submit();
      }
  }

  function confirmBase(){
      return confirm("Are you sure you want to set this unit as the base unit? This will remove the base status from the current base unit.");
  }

  function saveToDraft() {
      // Finds the closest parent form element holding this button
      // const form = buttonElement.closest('form');
      const form = document.querySelector('#saleForm');
      // Change this URL to your specific backend draft destination
      form.action = "<?= base_url('Admin/Sales/saleSaveAction')?>";

      // Submits the form data to the new URL
      form.submit();
  }

  //new invoice view
  function calculateTotals() {
      // Subtotal
      let totalAmount = parseFloat($('#totalAmount').val()) || 0;

      // Discount %
      let discount = parseFloat($('#discount').val()) || 0;

      // VAT %
      let vat = parseFloat($('#vat').val()) || 0;

      // Calculate discount
      let discountAmount = (totalAmount * discount) / 100;

      // Amount after discount
      let afterDiscount = totalAmount - discountAmount;

      // Calculate VAT after discount
      let vatAmount = (afterDiscount * vat) / 100;

      // Display values
      $('#discountAmount').text('৳ ' + discountAmount.toFixed(2) + ' /-');
      $('#discountAmount2').text('৳ ' + discountAmount.toFixed(2) + ' /-');
      $('#vatAmount').text('৳ ' + vatAmount.toFixed(2) + ' /-');
      $('#vatAmount2').text('৳ ' + vatAmount.toFixed(2) + ' /-');
  }

  // Run calculation
  $(document).ready(function () {
      calculateTotals();
  });
  //new invoice view




  // purchase data calculate
  function findResultPurchase(explicitVal) {
      var search_text = (explicitVal !== undefined && typeof explicitVal === 'string') ? explicitVal : $('#keyWord').val();
      if (search_text === undefined || search_text === null) {
          search_text = $('#keyWord').val() || '';
      }
      search_text = $.trim(search_text);
      if (search_text == "") {
          $('#result').empty();
      } else {
          $.ajax({
              type: "POST",
              url: "<?php echo site_url('Admin/Purchase/search_prod') ?>",
              data: {keyWord: search_text},

              success: function(html) {
                  $("#result").html(html).show();
              }
          });
      }
  }

  function calculateRow(row) {
      var qty = parseFloat($(row).find('input[name="qty[]"]').val()) || 0;
      var price = parseFloat($(row).find('input[name="unitPrice[]"]').val()) || 0;
      var conversionFactor = parseFloat($(row).find('input[name="conversion_factore[]"]').val()) || 0;

      // qty is base quantity, so convert to KG
      // Example: 3002000 / 1000 = 3002 KG
      // var conversionFactor = 1000;
      var actualQty = qty / conversionFactor;

      var subtotal = actualQty * price;

      $(row).find('input[name="subtotal[]"]').val(subtotal.toFixed(2));
      $(row).find('input[name="suballtotal[]"]').val(subtotal.toFixed(2));

      var index = $(row).index();
      $('#subtl_' + index).text(
          subtotal.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
          })
      );
      totalDue();
      checkDue()
      return subtotal;
  }

  function calculateGrandTotal() {
      var grandTotal = 0;

      $('#TFtable tbody tr').each(function () {
          grandTotal += calculateRow(this);
      });
      $('#totalPrice').val(grandTotal.toFixed(2));

      totalDue();
      checkDue();
  }

  // Unit price change
  $(document).on('input', 'input[name="unitPrice[]"]', function () {
      calculateGrandTotal();
      checkDue();
  });
  // Cash / Bank change
  $(document).on('input', '#cash, #bank', function () {
      totalDue();
      checkDue();
  });

  function priceMakeBasePurchase(val, key, printId) {
      var price = parseFloat(val) || 0;
      var factor = parseFloat(key) || 1;

      // KG price -> base unit price
      var basePrice = price / factor;
      $('#qtyUp_' + printId).val(basePrice.toFixed(8));

      calculateGrandTotal();
  }

  function checkDue() {
      var totalDue = parseFloat($('#totaldue').val()) || 0;

      if (totalDue < 0) {
          $('#createBtn').hide();
      } else {
          $('#createBtn').show();
      }
  }

  function priceMakeBaseSalePrice(val, key, printId) {
      var price = parseFloat(val) || 0;
      var factor = parseFloat(key) || 1;

      // KG price -> base unit price
      var basePrice = price / factor;
      $('#salePrice_' + printId).val(basePrice.toFixed(8));
  }

// Invoice Settings: dynamic add/remove for multi-value fields (gmail, name & phone)
  function invoiceMultiAdd(btn){
      var target = $(btn).data('target');
      var $container = $('.invoice-multi[data-label="' + target + '"]');
      if (!$container.length) { return; }
      var $row = $container.find('.invoice-multi-row').first().clone();
      $row.find('input').val('');
      $container.append($row);
  }

  function invoiceMultiRemove(btn){
      var $container = $(btn).closest('.invoice-multi');
      var $row = $(btn).closest('.invoice-multi-row');
      if ($container.find('.invoice-multi-row').length > 1) {
          $row.remove();
      } else {
          $row.find('input').val('');
      }
  }

</script>






