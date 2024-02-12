@extends('backend.layout.main')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<section class="forms">
    <div class="container-fluid">
        @if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Tambah Produk</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>Inputan yang ditandai dengan * wajib diisi.</small>
                        </p>
                        <form action="{{route('products.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tipe Produk *</strong> </label>
                                                <div class="input-group">
                                                    <select name="type" required class="form-control selectpicker"
                                                        id="type">
                                                        <option value="standard">Standard</option>
                                                        {{-- <option value="combo">Paket</option> --}}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Produk *</strong> </label>
                                                <input type="text" name="name" class="form-control" id="name" aria-describedby="name" required value="{{old('name')}}">
                                                <span class="validation-msg" id="name-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kode Produk *</strong> </label>
                                                <div class="input-group">
                                                    <input type="text" name="code" class="form-control" id="code"
                                                        aria-describedby="code" required value="{{old('code')}}">
                                                    <div class="input-group-append">
                                                        <button id="genbutton" type="button"
                                                            class="btn btn-sm btn-default"
                                                            title="{{trans('file.Generate')}}"><i
                                                                class="fa fa-refresh"></i></button>
                                                    </div>
                                                </div>
                                                <span class="validation-msg" id="code-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kategori *</strong> </label>
                                                <div class="input-group">
                                                    <select name="category_id" required
                                                        class="selectpicker form-control" data-live-search="true"
                                                        data-live-search-style="begins" title="Select Category...">
                                                        @foreach($lims_category_list as $category)
                                                        <option value="{{$category->id}}" {{old('category_id') == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Satuan Produk *</strong> </label>
                                            <div class="input-group">
                                                <select required class="form-control selectpicker" name="unit_id">
                                                    <option value="" disabled selected>Select Product Unit...</option>
                                                    @foreach($lims_unit_list as $unit)
                                                    @if($unit->base_unit==null)
                                                    <option value="{{$unit->id}}">{{$unit->unit_name}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="validation-msg"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Gambar Produk</strong> </label> <i
                                                    class="dripicons-question" data-toggle="tooltip"
                                                    title="Upload gambar dengan format .jpeg, .jpg, .png, .gif."></i>
                                                {{-- <div id="imageUpload" class="dropzone"></div> --}}
                                                <input type="file" class="form-control" name="image">
                                                <span class="validation-msg" id="image-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div id="combo" class="col-md-12 mb-1">
                                    <label>Tambah Produk</label>
                                    <div class="search-box input-group mb-3">
                                        <button class="btn btn-secondary"><i class="fa fa-barcode"></i></button>
                                        <input type="text" name="product_code_name" id="lims_productcodeSearch"
                                            placeholder="Pilih produk..." class="form-control" />
                                    </div>
                                    <label>Produk Kombo</label>
                                    <div class="table-responsive">
                                        <table id="myTable" class="table table-hover order-list">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('file.product')}}</th>
                                                    <th>{{trans('file.Quantity')}}</th>
                                                    <th>{{trans('file.Unit Price')}}</th>
                                                    <th><i class="dripicons-trash"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="unit" class="col-md-12">
                                    <div class="row ">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Harga Produk *</strong> </label>
                                                <input type="number" name="price" required class="form-control" step="any" value="{{old('price')}}">
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Detail Produk</label>
                                                <input name="product_details" class="form-control" rows="3" value="{{old('product_details')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Bahan Baku</label>
                                            <div class="search-box input-group mb-4">
                                                {{-- <button class="btn btn-secondary"><i class="fa fa-barcode"></i></button> --}}
                                                {{-- <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Pilih bahan baku..." class="form-control" /> --}}
                                                <select class="form-control selectpicker" name="ingredients[]" multiple data-live-search="true" data-live-search-style="begins">
                                                    {{-- <option value="" disabled>Select Product Unit...</option> --}}
                                                    @foreach($ingredients as $ingredient)
                                                    @if($ingredient->base_unit==null)
                                                    <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>
                                    </div>
                                    @if($roleName == 'Superadmin')
                                    <div class="col-md-12 mt-2" id="diffPrice-option">
                                        <h5><input name="is_diffPrice" type="checkbox" id="is-diffPrice" value="1">&nbsp;
                                            Produk ini punya harga berbeda untuk cabang berbeda</h5>
                                    </div>
                                    @endif
                                    <div class="col-md-6" id="diffPrice-section">
                                        <div class="table-responsive ml-2">
                                            <table id="diffPrice-table" class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Cabang</th>
                                                        <th>Harga</th>
                                                    </tr>
                                                    @foreach($lims_warehouse_list as $warehouse)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="warehouse_id[]"
                                                                value="{{$warehouse->id}}">
                                                            {{$warehouse->name}}
                                                        </td>
                                                        <td><input type="number" name="diff_price[]" class="form-control">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-group mt-3 mr-2">
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Kembali</a>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="submit" value="Submit" id=""
                                            class="btn btn-primary">
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$( '#multiple-select-field' ).select2( {
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    closeOnSelect: false,
} );
    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-create-menu").addClass("active");

    @if(config('database.connections.saleprosaas_landlord'))
    numberOfProduct = <?php echo json_encode($numberOfProduct)?>;
    $.ajax({
        type: 'GET',
        async: false,
        url: '{{route("package.fetchData", $general_setting->package_id)}}',
        success: function(data) {
            if(data['number_of_product'] > 0 && data['number_of_product'] <= numberOfProduct) {
                localStorage.setItem("message", "You don't have permission to create another product as you already exceed the limit! Subscribe to another package if you wants more!");
                location.href = "{{route('products.index')}}";
            }
        }
    });
    @endif

    $("#digital").hide();
    $("#combo").hide();
    $("#variant-section").hide();
    $("#initial-stock-section").hide();
    $("#diffPrice-section").hide();
    $("#promotion_price").hide();
    $("#start_date").hide();
    $("#last_date").hide();
    var variantPlaceholder = <?php echo json_encode(trans('file.Enter variant value seperated by comma')); ?>;
    var variantIds = [];
    var combinations = [];
    var oldCombinations = [];
    var oldAdditionalCost = [];
    var oldAdditionalPrice = [];
    var step;
    var numberOfWarehouse = <?php echo json_encode(count($lims_warehouse_list)) ?>;

    $('[data-toggle="tooltip"]').tooltip();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#genbutton').on("click", function(){
        $.get('gencode', function(data){
            $("input[name='code']").val(data);
        });
    });


    //start variant related js
    $(function() {
        $('.type-variant').tagsInput();
    });

    (function($) {
        var delimiter = [];
        var inputSettings = [];
        var callbacks = [];

        $.fn.addTag = function(value, options) {
            options = jQuery.extend({
                focus: false,
                callback: true
            }, options);
            this.each(function() {
                var id = $(this).attr('id');
                var tagslist = $(this).val().split(_getDelimiter(delimiter[id]));
                if (tagslist[0] === '') tagslist = [];

                value = jQuery.trim(value);

                if ((inputSettings[id].unique && $(this).tagExist(value)) || !_validateTag(value, inputSettings[id], tagslist, delimiter[id])) {
                    $('#' + id + '_tag').addClass('error');
                    return false;
                }

                $('<span>', {class: 'tag'}).append(
                    $('<span>', {class: 'tag-text'}).text(value),
                        $('<button>', {class: 'tag-remove'}).click(function() {
                            return $('#' + id).removeTag(encodeURI(value));
                        })
                        ).insertBefore('#' + id + '_addTag');
                        tagslist.push(value);

                        $('#' + id + '_tag').val('');
                        if (options.focus) {
                            $('#' + id + '_tag').focus();
                        } else {
                            $('#' + id + '_tag').blur();
                        }

                        $.fn.tagsInput.updateTagsField(this, tagslist);

                        if (options.callback && callbacks[id] && callbacks[id]['onAddTag']) {
                            var f = callbacks[id]['onAddTag'];
                            f.call(this, this, value);
                        }

                        if (callbacks[id] && callbacks[id]['onChange']) {
                            var i = tagslist.length;
                            var f = callbacks[id]['onChange'];
                            f.call(this, this, value);
                        }

                        $(".type-variant").each(function(index) {
                            variantIds.splice(index, 1, $(this).attr('id'));
                        });

                       rownumber = $('table.variant-list tbody tr:last').index();
                        if(rownumber > -1) {
                            oldCombinations = [];
                            oldAdditionalCost = [];
                            oldAdditionalPrice = [];
                            $(".variant-name").each(function(i) {
                                oldCombinations.push($(this).text());
                                oldAdditionalCost.push($('table.variant-list tbody tr:nth-child(' + (i + 1) + ')').find('.additional-cost').val());
                                oldAdditionalPrice.push($('table.variant-list tbody tr:nth-child(' + (i + 1) + ')').find('.additional-price').val());
                            });
                        }
                        $("table.variant-list tbody").remove();
                        var newBody = $("<tbody>");
                            for(i = 0; i < combinations.length; i++) {
                                var variant_name = combinations[i];
                                var item_code = variant_name+'-'+$("#code").val();
                                var newRow = $("<tr>");
                                    var cols = '';
                                    cols += '<td class="variant-name">'+variant_name+'<input type="hidden" name="variant_name[]" value="' + variant_name + '" /></td>';
                                    cols += '<td><input type="text" class="form-control item-code" name="item_code[]" value="'+item_code+'" /></td>';
                                    //checking if this variant already exist in the variant table
                                    oldIndex = oldCombinations.indexOf(combinations[i]);
                                    if(oldIndex >= 0) {
                                        cols += '<td><input type="number" class="form-control additional-cost" name="additional_cost[]" value="'+oldAdditionalCost[oldIndex]+'" step="any" /></td>';
                                        cols += '<td><input type="number" class="form-control additional-price" name="additional_price[]" value="'+oldAdditionalPrice[oldIndex]+'" step="any" /></td>';
                                    }
                                    else {
                                        cols += '<td><input type="number" class="form-control additional-cost" name="additional_cost[]" value="" step="any" /></td>';
                                        cols += '<td><input type="number" class="form-control additional-price" name="additional_price[]" value="" step="any" /></td>';
                                    }
                                    newRow.append(cols);
                                    newBody.append(newRow);
                                }
                                $("table.variant-list").append(newBody);
                                //end custom code
                            });
                            return false;
                        };

                        $.fn.removeTag = function(value) {
                            value = decodeURI(value);

                            this.each(function() {
                                var id = $(this).attr('id');

                                var old = $(this).val().split(_getDelimiter(delimiter[id]));

                                $('#' + id + '_tagsinput .tag').remove();

                                var str = '';
                                for (i = 0; i < old.length; ++i) {
                                    if (old[i] != value) {
                                        str = str + _getDelimiter(delimiter[id]) + old[i];
                                    }
                                }

                                $.fn.tagsInput.importTags(this, str);

                                if (callbacks[id] && callbacks[id]['onRemoveTag']) {
                                    var f = callbacks[id]['onRemoveTag'];
                                    f.call(this, this, value);
                                }
                            });

                            return false;
                        };

                        $.fn.tagExist = function(val) {
                            var id = $(this).attr('id');
                            var tagslist = $(this).val().split(_getDelimiter(delimiter[id]));
                            return (jQuery.inArray(val, tagslist) >= 0);
                        };

                        $.fn.importTags = function(str) {
                            var id = $(this).attr('id');
                            $('#' + id + '_tagsinput .tag').remove();
                            $.fn.tagsInput.importTags(this, str);
                        };

                        $.fn.tagsInput = function(options) {
                            var settings = jQuery.extend({
                                interactive: true,
                                placeholder: variantPlaceholder,
                                minChars: 0,
                                maxChars: null,
                                limit: null,
                                validationPattern: null,
                                width: 'auto',
                                height: 'auto',
                                autocomplete: null,
                                hide: true,
                                delimiter: ',',
                                unique: true,
                                removeWithBackspace: true
                            }, options);

                            var uniqueIdCounter = 0;

                            this.each(function() {
                                if (typeof $(this).data('tagsinput-init') !== 'undefined') return;

                                $(this).data('tagsinput-init', true);

                                if (settings.hide) $(this).hide();

                                var id = $(this).attr('id');
                                if (!id || _getDelimiter(delimiter[$(this).attr('id')])) {
                                    id = $(this).attr('id', 'tags' + new Date().getTime() + (++uniqueIdCounter)).attr('id');
                                }

                                var data = jQuery.extend({
                                    pid: id,
                                    real_input: '#' + id,
                                    holder: '#' + id + '_tagsinput',
                                    input_wrapper: '#' + id + '_addTag',
                                    fake_input: '#' + id + '_tag'
                                }, settings);

                                delimiter[id] = data.delimiter;
                                inputSettings[id] = {
                                    minChars: settings.minChars,
                                    maxChars: settings.maxChars,
                                    limit: settings.limit,
                                    validationPattern: settings.validationPattern,
                                    unique: settings.unique
                                };

                                if (settings.onAddTag || settings.onRemoveTag || settings.onChange) {
                                    callbacks[id] = [];
                                    callbacks[id]['onAddTag'] = settings.onAddTag;
                                    callbacks[id]['onRemoveTag'] = settings.onRemoveTag;
                                    callbacks[id]['onChange'] = settings.onChange;
                                }

                                var markup = $('<div>', {id: id + '_tagsinput', class: 'tagsinput'}).append(
                                    $('<div>', {id: id + '_addTag'}).append(
                                        settings.interactive ? $('<input>', {id: id + '_tag', class: 'tag-input', value: '', placeholder: settings.placeholder}) : null
                                        )
                                        );

                                        $(markup).insertAfter(this);

                                        $(data.holder).css('width', settings.width);
                                        $(data.holder).css('min-height', settings.height);
                                        $(data.holder).css('height', settings.height);

                                        if ($(data.real_input).val() !== '') {
                                            $.fn.tagsInput.importTags($(data.real_input), $(data.real_input).val());
                                        }

                                        // Stop here if interactive option is not chosen
                                        if (!settings.interactive) return;

                                        $(data.fake_input).val('');
                                        $(data.fake_input).data('pasted', false);

                                        $(data.fake_input).on('focus', data, function(event) {
                                            $(data.holder).addClass('focus');

                                            if ($(this).val() === '') {
                                                $(this).removeClass('error');
                                            }
                                        });

                                        $(data.fake_input).on('blur', data, function(event) {
                                            $(data.holder).removeClass('focus');
                                        });

                                        if (settings.autocomplete !== null && jQuery.ui.autocomplete !== undefined) {
                                            $(data.fake_input).autocomplete(settings.autocomplete);
                                            $(data.fake_input).on('autocompleteselect', data, function(event, ui) {
                                                $(event.data.real_input).addTag(ui.item.value, {
                                                    focus: true,
                                                    unique: settings.unique
                                                });

                                                return false;
                                            });

                                            $(data.fake_input).on('keypress', data, function(event) {
                                                if (_checkDelimiter(event)) {
                                                    $(this).autocomplete("close");
                                                }
                                            });
                                        } else {
                                            $(data.fake_input).on('blur', data, function(event) {
                                                $(event.data.real_input).addTag($(event.data.fake_input).val(), {
                                                    focus: true,
                                                    unique: settings.unique
                                                });

                                                return false;
                                            });
                                        }

                                        // If a user types a delimiter create a new tag
                                        $(data.fake_input).on('keypress', data, function(event) {
                                            if (_checkDelimiter(event)) {
                                                event.preventDefault();

                                                $(event.data.real_input).addTag($(event.data.fake_input).val(), {
                                                    focus: true,
                                                    unique: settings.unique
                                                });

                                                return false;
                                            }
                                        });

                                        $(data.fake_input).on('paste', function () {
                                            $(this).data('pasted', true);
                                        });

                                        // If a user pastes the text check if it shouldn't be splitted into tags
                                        $(data.fake_input).on('input', data, function(event) {
                                            if (!$(this).data('pasted')) return;

                                            $(this).data('pasted', false);

                                            var value = $(event.data.fake_input).val();

                                            value = value.replace(/\n/g, '');
                                            value = value.replace(/\s/g, '');

                                            var tags = _splitIntoTags(event.data.delimiter, value);

                                            if (tags.length > 1) {
                                                for (var i = 0; i < tags.length; ++i) {
                                                    $(event.data.real_input).addTag(tags[i], {
                                                        focus: true,
                                                        unique: settings.unique
                                                    });
                                                }

                                                return false;
                                            }
                                        });

                                        // Deletes last tag on backspace
                                        data.removeWithBackspace && $(data.fake_input).on('keydown', function(event) {
                                            if (event.keyCode == 8 && $(this).val() === '') {
                                                event.preventDefault();
                                                var lastTag = $(this).closest('.tagsinput').find('.tag:last > span').text();
                                                var id = $(this).attr('id').replace(/_tag$/, '');
                                                $('#' + id).removeTag(encodeURI(lastTag));
                                                $(this).trigger('focus');
                                            }
                                        });

                                        // Removes the error class when user changes the value of the fake input
                                        $(data.fake_input).keydown(function(event) {
                                            // enter, alt, shift, esc, ctrl and arrows keys are ignored
                                            if (jQuery.inArray(event.keyCode, [13, 37, 38, 39, 40, 27, 16, 17, 18, 225]) === -1) {
                                                $(this).removeClass('error');
                                            }
                                        });
                                    });

                                    return this;
                                };

                                $.fn.tagsInput.updateTagsField = function(obj, tagslist) {
                                    var id = $(obj).attr('id');
                                    $(obj).val(tagslist.join(_getDelimiter(delimiter[id])));
                                };

                                $.fn.tagsInput.importTags = function(obj, val) {
                                    $(obj).val('');

                                    var id = $(obj).attr('id');
                                    var tags = _splitIntoTags(delimiter[id], val);

                                    for (i = 0; i < tags.length; ++i) {
                                        $(obj).addTag(tags[i], {
                                            focus: false,
                                            callback: false
                                        });
                                    }

                                    if (callbacks[id] && callbacks[id]['onChange']) {
                                        var f = callbacks[id]['onChange'];
                                        f.call(obj, obj, tags);
                                    }
                                };

                                var _getDelimiter = function(delimiter) {
                                    if (typeof delimiter === 'undefined') {
                                        return delimiter;
                                    } else if (typeof delimiter === 'string') {
                                        return delimiter;
                                    } else {
                                        return delimiter[0];
                                    }
                                };

                                var _validateTag = function(value, inputSettings, tagslist, delimiter) {
                                    var result = true;

                                    if (value === '') result = false;
                                    if (value.length < inputSettings.minChars) result = false;
                                    if (inputSettings.maxChars !== null && value.length > inputSettings.maxChars) result = false;
                                    if (inputSettings.limit !== null && tagslist.length >= inputSettings.limit) result = false;
                                    if (inputSettings.validationPattern !== null && !inputSettings.validationPattern.test(value)) result = false;

                                    if (typeof delimiter === 'string') {
                                        if (value.indexOf(delimiter) > -1) result = false;
                                    } else {
                                        $.each(delimiter, function(index, _delimiter) {
                                            if (value.indexOf(_delimiter) > -1) result = false;
                                            return false;
                                        });
                                    }

                                    return result;
                                };

                                var _checkDelimiter = function(event) {
                                    var found = false;

                                    if (event.which === 13) {
                                        return true;
                                    }

                                    if (typeof event.data.delimiter === 'string') {
                                        if (event.which === event.data.delimiter.charCodeAt(0)) {
                                            found = true;
                                        }
                                    } else {
                                        $.each(event.data.delimiter, function(index, delimiter) {
                                            if (event.which === delimiter.charCodeAt(0)) {
                                                found = true;
                                            }
                                        });
                                    }

                                    return found;
                                };

                                var _splitIntoTags = function(delimiter, value) {
                                    if (value === '') return [];

                                    if (typeof delimiter === 'string') {
                                        return value.split(delimiter);
                                    } else {
                                        var tmpDelimiter = '∞';
                                        var text = value;

                                        $.each(delimiter, function(index, _delimiter) {
                                            text = text.split(_delimiter).join(tmpDelimiter);
                                        });

                                        return text.split(tmpDelimiter);
                                    }

                                    return [];
                                };
                            })(jQuery);
                            //end of variant related js



                            $('select[name="type"]').on('change', function() {
                                if($(this).val() == 'combo'){
                                    $("input[name='cost']").prop('required',false);
                                    $("select[name='unit_id']").prop('required',false);
                                    hide();
                                    $("#combo").show(300);
                                    $("input[name='price']").prop('disabled',true);
                                    $("#is-variant").prop("checked", false);
                                    $("#is-diffPrice").prop("checked", false);
                                    $("#variant-section, #variant-option, #diffPrice-option, #diffPrice-section").hide(300);
                                }
                                else if($(this).val() == 'digital'){
                                    $("input[name='cost']").prop('required',false);
                                    $("select[name='unit_id']").prop('required',false);
                                    $("input[name='file']").prop('required',true);
                                    hide();
                                    $("#digital").show(300);
                                    $("#combo").hide(300);
                                    $("input[name='price']").prop('disabled',false);
                                    $("#is-variant").prop("checked", false);
                                    $("#is-diffPrice").prop("checked", false);
                                    $("#variant-section, #variant-option, #diffPrice-option, #diffPrice-section, #batch-option").hide(300);
                                }
                                else if($(this).val() == 'service') {
                                    $("input[name='cost']").prop('required',false);
                                    $("select[name='unit_id']").prop('required',false);
                                    $("input[name='file']").prop('required',true);
                                    hide();
                                    $("#combo").hide(300);
                                    $("#digital").hide(300);
                                    $("input[name='price']").prop('disabled',false);
                                    $("#is-variant").prop("checked", false);
                                    $("#is-diffPrice").prop("checked", false);
                                    $("#variant-section, #variant-option, #diffPrice-option, #diffPrice-section, #batch-option, #imei-option").hide(300);
                                }
                                else if($(this).val() == 'standard') {
                                    $("input[name='cost']").prop('required',true);
                                    $("select[name='unit_id']").prop('required',true);
                                    $("input[name='file']").prop('required',false);
                                    $("#cost").show(300);
                                    $("#unit").show(300);
                                    $("#alert-qty").show(300);
                                    $("#variant-option, #diffPrice-option, #batch-option, #imei-option").show(300);
                                    $("#digital").hide(300);
                                    $("#combo").hide(300);
                                    $("input[name='price']").prop('disabled',false);
                                }
                            });

                            $('select[name="unit_id"]').on('change', function() {

                                unitID = $(this).val();
                                if(unitID) {
                                    populate_category(unitID);
                                }else{
                                    $('select[name="sale_unit_id"]').empty();
                                    $('select[name="purchase_unit_id"]').empty();
                                }
                            });
                            <?php $productArray = []; ?>
                            var lims_product_code = [
                            <?php
                            echo  '"'.implode('","', $productArray).'"';
                            ?> ];

                            var lims_productcodeSearch = $('#lims_productcodeSearch');

                            lims_productcodeSearch.autocomplete({
                                source: function(request, response) {
                                    var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
                                    response($.grep(lims_product_code, function(item) {
                                        return matcher.test(item);
                                    }));
                                },
                                select: function(event, ui) {
                                    var data = ui.item.value;
                                    $.ajax({
                                        type: 'GET',
                                        url: 'lims_product_search',
                                        data: {
                                            data: data
                                        },
                                        success: function(data) {
                                            //console.log(data);
                                            var flag = 1;
                                            $(".product-id").each(function() {
                                                if ($(this).val() == data[8]) {
                                                    alert('Duplicate input is not allowed!')
                                                    flag = 0;
                                                }
                                            });
                                            $("input[name='product_code_name']").val('');
                                            if(flag){
                                                var newRow = $("<tr>");
                                                    var cols = '';
                                                    cols += '<td>' + data[0] +' [' + data[1] + ']</td>';
                                                    cols += '<td><input type="number" class="form-control qty" name="product_qty[]" value="1" step="any"/></td>';
                                                    cols += '<td><input type="number" class="form-control unit_price" name="unit_price[]" value="' + data[2] + '" step="any"/></td>';
                                                    cols += '<td><button type="button" class="ibtnDel btn btn-sm btn-danger">X</button></td>';
                                                    cols += '<input type="hidden" class="product-id" name="product_id[]" value="' + data[8] + '"/>';
                                                    cols += '<input type="hidden" class="" name="variant_id[]" value="' + data[9] + '"/>';

                                                    newRow.append(cols);
                                                    $("table.order-list tbody").append(newRow);
                                                    calculate_price();
                                                }
                                            }
                                        });
                                    }
                                });

                                //Change quantity or unit price
                                $("#myTable").on('input', '.qty , .unit_price', function() {
                                    calculate_price();
                                });

                                //Delete product
                                $("table.order-list tbody").on("click", ".ibtnDel", function(event) {
                                    $(this).closest("tr").remove();
                                    calculate_price();
                                });

                                function hide() {
                                    $("#cost").hide(300);
                                    $("#unit").hide(300);
                                    $("#alert-qty").hide(300);
                                }

                                function calculate_price() {
                                    var price = 0;
                                    $(".qty").each(function() {
                                        rowindex = $(this).closest('tr').index();
                                        quantity =  $(this).val();
                                        unit_price = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .unit_price').val();
                                        price += quantity * unit_price;
                                    });
                                    $('input[name="price"]').val(price);
                                }

                                function populate_category(unitID){
                                    $.ajax({
                                        url: 'saleunit/'+unitID,
                                        type: "GET",
                                        dataType: "json",
                                        success:function(data) {
                                            $('select[name="sale_unit_id"]').empty();
                                            $('select[name="purchase_unit_id"]').empty();
                                            $.each(data, function(key, value) {
                                                $('select[name="sale_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                                                $('select[name="purchase_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                                            });
                                            $('.selectpicker').selectpicker('refresh');
                                        },
                                    });
                                }

                                $("input[name='is_initial_stock']").on("change", function () {
                                    if ($(this).is(':checked')) {
                                        if(numberOfWarehouse > 0)
                                        $("#initial-stock-section").show(300);
                                        else {
                                            alert('Please create warehouse first before adding stock!');
                                            $(this).prop("checked", false);
                                        }
                                    }
                                    else {
                                        $("#initial-stock-section").hide(300);
                                    }
                                });

                                $("input[name='is_batch']").on("change", function () {
                                    if ($(this).is(':checked')) {
                                        $("#variant-option").hide(300);
                                    }
                                    else
                                    $("#variant-option").show(300);
                                });

                                $("input[name='is_variant']").on("change", function () {
                                    if ($(this).is(':checked')) {
                                        $("#variant-section").show(300);
                                        $("#batch-option").hide(300);
                                        $(".variant-field").prop("required", true);
                                    }
                                    else {
                                        $("#variant-section").hide(300);
                                        $("#batch-option").show(300);
                                        $(".variant-field").prop("required", false);
                                    }
                                });

                                $("input[name='is_diffPrice']").on("change", function () {
                                    if ($(this).is(':checked')) {
                                        $("#diffPrice-section").show(300);
                                    }
                                    else
                                    $("#diffPrice-section").hide(300);
                                });

                                $( "#promotion" ).on( "change", function() {
                                    if ($(this).is(':checked')) {
                                        $("#starting_date").val($.datepicker.formatDate('dd-mm-yy', new Date()));
                                        $("#promotion_price").show(300);
                                        $("#start_date").show(300);
                                        $("#last_date").show(300);
                                    }
                                    else {
                                        $("#promotion_price").hide(300);
                                        $("#start_date").hide(300);
                                        $("#last_date").hide(300);
                                    }
                                });

                                var starting_date = $('#starting_date');
                                starting_date.datepicker({
                                    format: "dd-mm-yyyy",
                                    startDate: "<?php echo date('d-m-Y'); ?>",
                                    autoclose: true,
                                    todayHighlight: true
                                });

                                var ending_date = $('#ending_date');
                                ending_date.datepicker({
                                    format: "dd-mm-yyyy",
                                    startDate: "<?php echo date('d-m-Y'); ?>",
                                    autoclose: true,
                                    todayHighlight: true
                                });

                                $(window).keydown(function(e){
                                    if (e.which == 13) {
                                        var $targ = $(e.target);

                                        if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
                                            var focusNext = false;
                                            $(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
                                                if (this === e.target) {
                                                    focusNext = true;
                                                }
                                                else if (focusNext){
                                                    $(this).focus();
                                                    return false;
                                                }
                                            });

                                            return false;
                                        }
                                    }
                                });
                                //dropzone portion
                                Dropzone.autoDiscover = false;

                                jQuery.validator.setDefaults({
                                    errorPlacement: function (error, element) {
                                        if(error.html() == 'Select Category...')
                                        error.html('This field is required.');
                                        $(element).closest('div.form-group').find('.validation-msg').html(error.html());
                                    },
                                    highlight: function (element) {
                                        $(element).closest('div.form-group').removeClass('has-success').addClass('has-error');
                                    },
                                    unhighlight: function (element, errorClass, validClass) {
                                        $(element).closest('div.form-group').removeClass('has-error').addClass('has-success');
                                        $(element).closest('div.form-group').find('.validation-msg').html('');
                                    }
                                });

                                function validate() {
                                    var product_code = $("input[name='code']").val();
                                    var barcode_symbology = $('select[name="barcode_symbology"]').val();
                                    var exp = /^\d+$/;

                                    if(!(product_code.match(exp)) && (barcode_symbology == 'UPCA' || barcode_symbology == 'UPCE' || barcode_symbology == 'EAN8' || barcode_symbology == 'EAN13') ) {
                                        alert('Product code must be numeric.');
                                        return false;
                                    }
                                    else if(product_code.match(exp)) {
                                        if(barcode_symbology == 'UPCA' && product_code.length > 11){
                                            alert('Product code length must be less than 12');
                                            return false;
                                        }
                                        else if(barcode_symbology == 'EAN8' && product_code.length > 7){
                                            alert('Product code length must be less than 8');
                                            return false;
                                        }
                                        else if(barcode_symbology == 'EAN13' && product_code.length > 12){
                                            alert('Product code length must be less than 13');
                                            return false;
                                        }
                                    }

                                    if( $("#type").val() == 'combo' ) {
                                        var rownumber = $('table.order-list tbody tr:last').index();
                                        if (rownumber < 0) {
                                            alert("Please insert product to table!")
                                            return false;
                                        }
                                    }
                                    if($("#is-variant").is(":checked")) {
                                        rowindex = $("table#variant-table tbody tr:last").index();
                                        if (rowindex < 0) {
                                            alert('This product has variant. Please insert variant to table');
                                            return false;
                                        }
                                    }
                                    $("input[name='price']").prop('disabled',false);
                                    return true;
                                }

                                $(".dropzone").sortable({
                                    items:'.dz-preview',
                                    cursor: 'grab',
                                    opacity: 0.5,
                                    containment: '.dropzone',
                                    distance: 20,
                                    tolerance: 'pointer',
                                    stop: function () {
                                        var queue = myDropzone.getAcceptedFiles();
                                        newQueue = [];
                                        $('#imageUpload .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                                            var name = el.innerHTML;
                                            queue.forEach(function(file) {
                                                if (file.name === name) {
                                                    newQueue.push(file);
                                                }
                                            });
                                        });
                                        myDropzone.files = newQueue;
                                    }
                                });

                                myDropzone = new Dropzone('div#imageUpload', {
                                    addRemoveLinks: true,
                                    autoProcessQueue: false,
                                    uploadMultiple: true,
                                    parallelUploads: 100,
                                    maxFilesize: 12,
                                    paramName: 'image',
                                    clickable: true,
                                    method: 'POST',
                                    url: '{{route('products.store')}}',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    renameFile: function(file) {
                                        var dt = new Date();
                                        var time = dt.getTime();
                                        return time + file.name;
                                    },
                                    acceptedFiles: ".jpeg,.jpg,.png,.gif",
                                    init: function () {
                                        var myDropzone = this;
                                        $('#submit-btn').on("click", function (e) {
                                            e.preventDefault();
                                            if ( $("#product-form").valid() && validate() ) {
                                                if(myDropzone.getAcceptedFiles().length) {
                                                    myDropzone.processQueue();
                                                }
                                                else {
                                                    var formData = new FormData();
                                                    var data = $("#product-form").serializeArray();
                                                    $.each(data, function (key, el) {
                                                        formData.append(el.name, el.value);
                                                    });
                                                    var file = $('#file')[0].files;
                                                    if(file.length > 0)
                                                    formData.append('file',file[0]);
                                                    $.ajax({
                                                        type:'POST',
                                                        url:'{{route('products.store')}}',
                                                        data: formData,
                                                        contentType: false,
                                                        processData: false,
                                                        success:function(response) {
                                                            //console.log(response);
                                                            location.href = '../products';
                                                        },
                                                        error:function(response) {
                                                            if(response.responseJSON.errors.name) {
                                                                $("#name-error").text(response.responseJSON.errors.name);
                                                            }
                                                            else if(response.responseJSON.errors.code) {
                                                                $("#code-error").text(response.responseJSON.errors.code);
                                                            }
                                                        },
                                                    });
                                                }
                                            }
                                        });

                                        this.on('sending', function (file, xhr, formData) {
                                            // Append all form inputs to the formData Dropzone will POST
                                            var data = $("#product-form").serializeArray();
                                            $.each(data, function (key, el) {
                                                formData.append(el.name, el.value);
                                            });
                                            var file = $('#file')[0].files;
                                            if(file.length > 0)
                                            formData.append('file',file[0]);
                                        });
                                    },
                                    error: function (file, response) {
                                        console.log(response);
                                        if(response.errors.name) {
                                            $("#name-error").text(response.errors.name);
                                            this.removeAllFiles(true);
                                        }
                                        else if(response.errors.code) {
                                            $("#code-error").text(response.errors.code);
                                            this.removeAllFiles(true);
                                        }
                                        else {
                                            try {
                                                var res = JSON.parse(response);
                                                if (typeof res.message !== 'undefined' && !$modal.hasClass('in')) {
                                                    $("#success-icon").attr("class", "fas fa-thumbs-down");
                                                    $("#success-text").html(res.message);
                                                    $modal.modal("show");
                                                } else {
                                                    if ($.type(response) === "string")
                                                    var message = response; //dropzone sends it's own error messages in string
                                                    else
                                                    var message = response.message;
                                                    file.previewElement.classList.add("dz-error");
                                                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                                                    _results = [];
                                                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                                                        node = _ref[_i];
                                                        _results.push(node.textContent = message);
                                                    }
                                                    return _results;
                                                }
                                            } catch (error) {
                                                console.log(error);
                                            }
                                        }
                                    },
                                    successmultiple: function (file, response) {
                                        location.href = '../products';
                                        //console.log(file, response);
                                    },
                                    completemultiple: function (file, response) {
                                        console.log(file, response, "completemultiple");
                                    },
                                    reset: function () {
                                        console.log("resetFiles");
                                        this.removeAllFiles(true);
                                    }
                                });

                            </script>
                            @endpush
