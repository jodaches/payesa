<?php
#app/Http/Admin/Controllers/ShopProductController.php
namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Http\Controllers\Controller;
use App\Models\ShopAttributeGroup;
use App\Models\ShopBrand;
use App\Models\ShopTax;
use App\Models\ShopCategory;
use App\Models\ShopLanguage;
use App\Models\ShopProduct;
use App\Models\ShopProductAttribute;
use App\Models\ShopProductBuild;
use App\Models\ShopProductCategory;
use App\Models\ShopProductDescription;
use App\Models\ShopProductGroup;
use App\Models\ShopProductImage;
use App\Models\ShopProductPromotion;
use App\Models\ShopSupplier;
use Validator;
use DB;

class ShopProductController extends Controller
{
    public const PROFITS_ROLES = ['gerente','administrator'];
    public $languages, $types, $kinds, $virtuals, $attributeGroup;

    public function __construct()
    {
        $this->languages = ShopLanguage::getList();
        $this->attributeGroup = ShopAttributeGroup::getList();
        $this->types = [
            SC_PRODUCT_NORMAL => trans('product.types.normal'),
            SC_PRODUCT_NEW => trans('product.types.new'),
            SC_PRODUCT_HOT => trans('product.types.hot'),
        ];
        $this->kinds = [
            SC_PRODUCT_SINGLE => trans('product.kinds.single'),
            SC_PRODUCT_BUILD => trans('product.kinds.build'),
            SC_PRODUCT_GROUP => trans('product.kinds.group'),
        ];
        $this->virtuals = [
            SC_VIRTUAL_PHYSICAL => trans('product.virtuals.physical'),
            SC_VIRTUAL_DOWNLOAD => trans('product.virtuals.download'),
            SC_VIRTUAL_ONLY_VIEW => trans('product.virtuals.only_view'),
            SC_VIRTUAL_SERVICE => trans('product.virtuals.service'),
        ];

    }

    public function index()
    {
        $allowProfits = Admin::user()->inRoles(self::PROFITS_ROLES);
        $data = [
            'title' => trans('product.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'menuRight' => [],
            'menuLeft' => [],
            'topMenuRight' => [],
            'topMenuLeft' => [],
            'urlDeleteItem' => route('admin_product.delete'),
            'removeList' => $allowProfits, // Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '', 
            'js' => '',
        ];

        $listTh = [
            'id' => trans('product.id'),
            'image' => trans('product.image'),
            'sku' => trans('product.sku'),
            'name' => trans('product.name'),
            'category' => trans('product.category'),
        ];
        if(sc_config('product_cost') && $allowProfits){
            $listTh['cost'] = trans('product.cost');
        }
        if(sc_config('product_price')){
            $listTh['price'] = trans('product.price');
        }
        $listTh['lower_price'] = trans('product.lower_price');

        if($allowProfits){
            $listTh['profits'] = trans('product.profits');
        }
        if(sc_config('product_promotion')){
            $listTh['price_promotion'] = trans('product.price_promotion');
        }
        if(sc_config('product_type')){
            $listTh['type'] = trans('product.type');
        }
        if(sc_config('product_kind')){
            $listTh['kind'] = trans('product.kind');
        }
        if(sc_config('product_virtual')){
            $listTh['virtual'] = trans('product.virtual');
        }
        $listTh['status'] = trans('product.status');
        $listTh['stock'] = trans('product.stock');
        if($allowProfits){
            $listTh['action'] = trans('product.admin.action');
        }

        $keyword = request('keyword') ?? '';

        $sort_order = request('sort_order') ?? 'id_desc';
        $category_filter = request('category_filter') ?? '';

        $arrSort = [
            'id__desc' => trans('product.admin.sort_order.id_desc'),
            'id__asc' => trans('product.admin.sort_order.id_asc'),
            'name__desc' => trans('product.admin.sort_order.name_desc'),
            'name__asc' => trans('product.admin.sort_order.name_asc'),
            'stock__asc' => trans('product.admin.sort_order.stock_asc'),
            'stock__desc' => trans('product.admin.sort_order.stock_desc'),
        ];


        $tableDescription = (new ShopProductDescription)->getTable();
        $tableProduct = (new ShopProduct)->getTable();
        $tableProductPromotion = (new ShopProductPromotion)->getTable();
        $tableProductCategory = (new ShopProductCategory)->table;

        $obj = (new ShopProduct)
            ->leftJoin($tableDescription, $tableDescription . '.product_id', $tableProduct . '.id')
            ->leftJoin($tableProductPromotion, function($join) use ($tableProductPromotion, $tableProduct){
                $join->on($tableProductPromotion . '.product_id', $tableProduct . '.id')
                    ->on($tableProductPromotion . '.status_promotion', DB::raw(1));
            })
            ->where($tableDescription . '.lang', sc_get_locale());

        if ($keyword) {
            $obj = $obj->where(function ($sql) use($tableDescription, $tableProduct, $keyword){
                $sql->where($tableDescription . '.name', 'like', '%' . $keyword . '%')
                ->orWhere($tableDescription . '.keyword', 'like', '%' . $keyword . '%')
                ->orWhere($tableDescription . '.description', 'like', '%' . $keyword . '%')
                ->orWhere($tableProduct . '.sku', 'like', '%' . $keyword . '%');
            });
        }
        if ((int) $category_filter) {
            $obj = $obj->join($tableProductCategory, $tableProductCategory . '.product_id', $tableProduct . '.id')
                ->where($tableProductCategory . '.category_id', $category_filter);
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->sort($field, $sort_field);
        } else {
            $obj = $obj->sort('id', 'desc');
        }
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $kind = $this->kinds[$row['kind']] ?? $row['kind'];
            if ($row['kind'] == SC_PRODUCT_BUILD) {
                $kind = '<span class="label label-success">' . $kind . '</span>';
            } elseif ($row['kind'] == SC_PRODUCT_GROUP) {
                $kind = '<span class="label label-danger">' . $kind . '</span>';
            }
            $type = $this->types[$row['type']] ?? $row['type'];
            if ($row['type'] == SC_PRODUCT_NEW) {
                $type = '<span class="label label-success">' . $type . '</span>';
            } elseif ($row['type'] == SC_PRODUCT_HOT) {
                $type = '<span class="label label-danger">' . $type . '</span>';
            }
            $arrName = [];
            foreach ($row->categories as $category) {
                
                $arrName[] = $category->descriptions()->where('lang', sc_get_locale())->first()->title;
            }
            $dataMap = [
                'id' => $row['id'],
                'image' => sc_image_render($row->getThumb(), '50px', '50px', $row['name']),
                'sku' => $row['sku'],
                'name' => $row['name'],
                'category' => implode(';<br>', $arrName),
                
            ];
            if(sc_config('product_cost') && $allowProfits){
                $dataMap['cost'] = $row['cost'];
            }
            if(sc_config('product_price')){
                $dataMap['price'] = $row['price'];
            }
            $dataMap['lower_price'] = $row['lower_price'];        
            if($allowProfits){
                $dataMap['profits'] = $row['price'] - $row['cost'] ;
            }
            if(sc_config('product_promotion')){
                $dataMap['price_promotion'] = $row['price_promotion'];
            }
            if(sc_config('product_type')){
                $dataMap['type'] = $type;
            }
            if(sc_config('product_kind')){
                $dataMap['kind'] = $kind;
            }
            if(sc_config('product_virtual')){
                $dataMap['virtual'] = $this->virtuals[$row['virtual']] ?? $row['virtual'];
            }
            $dataMap['status'] = $row['status'] ? '<span class="label label-success">ON</span>' : '<span class="label label-danger">OFF</span>';
            $dataMap['stock'] = $row['stock'];
            if($allowProfits){
                $dataMap['action'] = '
                <a href="' . route('admin_product.edit', ['id' => $row['id']]) . '">
                <span title="' . trans('product.admin.edit') . '" type="button" class="btn btn-flat btn-primary">
                <i class="fa fa-edit"></i>
                </span>
                </a>&nbsp;

                <span onclick="deleteItem(' . $row['id'] . ');"  title="' . trans('admin.delete') . '" class="btn btn-flat btn-danger">
                <i class="fa fa-trash"></i>
                </span>';
            }
            $dataTr[] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links('admin.component.pagination');
        $data['resultItems'] = trans('product.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

//menuRight
    if($allowProfits){
        $data['menuRight'][] = '<a href="' . route('admin_product.create') . '" class="btn btn-success btn-flat" title="New" id="button_create_new">
        <i class="fa fa-plus"></i><span class="hidden-xs">' . trans('admin.add_new') . '</span>
        </a>';
        if(sc_config('ImportProduct')) {
            $data['menuRight'][] = '<a href="' . route('admin_import_product.index') . '" class="btn  btn-success  btn-flat" title="New">
            <i class="fa fa fa-floppy-o"></i> <span class="hidden-xs">' . trans('admin.add_new_multi') . '</span>
            </a>';
        }
    }
//=menuRight

//menuSort        
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['optionSort'] = $optionSort;
        $data['urlSort'] = route('admin_product.index');
//=menuSort

//topMenuRight
        $categories = ShopCategory::pluck('alias','id');
        $categoryFliterOptions = '';
        foreach ($categories as $catId => $catAlias) {
            $categoryFliterOptions .= '<option  ' . (($category_filter == $catId) ? "selected" : "") . ' value="' . $catId . '">' . $catAlias . '</option>';
        }

        $data['topMenuRight'][] ='
                <form action="' . route('admin_product.index') . '" id="button_search">
                   <div onclick="$(this).submit();" class="btn-group pull-right">
                           <a class="btn btn-flat btn-primary" title="Refresh">
                              <i class="fa  fa-search"></i>
                              <span class="hidden-xs"> ' . trans('admin.search') . '</span>
                           </a>
                   </div>
                   <div class="btn-group pull-right">
                         <div class="form-group">
                           <input style="min-width:225px" type="text" name="keyword" class="form-control" placeholder="' . trans('product.admin.search_place') . '" value="' . $keyword . '">
                         </div>
                   </div>

                   <div class="btn-group pull-right"  style="margin-right: 10px">
                        <div class="form-group">
                           <select class="form-control" name="category_filter">
                             <option value="">' . trans('product.category') . '</option>
                             ' . $categoryFliterOptions . '
                            </select>
                        </div>
                    </div>

                </form>';
//=topMenuRight

        return view('admin.screen.list')
            ->with($data);
    }

/**
 * Form create new order in admin
 * @return [type] [description]
 */
    public function create()
    {
        $listProductSingle = (new ShopProduct)->getProductSingle()->getData(['keyBy' => 'id'])->toArray();

        // html select product group
        $htmlSelectGroup = '<div class="select-product">';
        $htmlSelectGroup .= '<table width="100%"><tr><td width="80%"><select class="form-control productInGroup select2" data-placeholder="' . trans('product.admin.select_product_in_group') . '" style="width: 100%;" name="productInGroup[]" >';
        $htmlSelectGroup .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectGroup .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectGroup .= '</select></td><td><span title="Remove" class="btn btn-flat btn-danger removeproductInGroup"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectGroup .= '</div>';
        //End select product group

        // html select product build
        $htmlSelectBuild = '<div class="select-product">';
        $htmlSelectBuild .= '<table width="100%"><tr><td width="70%"><select class="form-control productInGroup select2" data-placeholder="' . trans('product.admin.select_product_in_build') . '" style="width: 100%;" name="productBuild[]" >';
        $htmlSelectBuild .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectBuild .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectBuild .= '</select></td><td style="width:100px"><input class="form-control"  type="number" name="productBuildQty[]" value="1" min=1></td><td><span title="Remove" class="btn btn-flat btn-danger removeproductBuild"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectBuild .= '</div>';
        //end select product build

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][]" value="attribute_value" class="form-control input-sm" placeholder="' . trans('product.admin.add_attribute_place') . '" /></td><td><br><span title="Remove" class="btn btn-flat btn-sm btn-danger removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        // html add more images
        $htmlMoreImage = '<div class="input-group"><input type="text" id="id_sub_image" name="sub_image[]" value="image_value" class="form-control input-sm sub_image" placeholder=""  /><span class="input-group-btn"><a data-input="id_sub_image" data-preview="preview_sub_image" data-type="product" class="btn btn-sm btn-primary lfm"><i class="fa fa-picture-o"></i> Choose</a></span></div><div id="preview_sub_image" class="img_holder"></div>';
        //end add more images

        $data = [
            'title' => trans('product.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans('product.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'categories' => (new ShopCategory)->getTreeCategories(),
            'brands' => (new ShopBrand)->getList(),
            'suppliers' => (new ShopSupplier)->getList(),
            'taxs' => (new ShopTax)->getList(),
            'types' => $this->types,
            'virtuals' => $this->virtuals,
            'kinds' => $this->kinds,
            'attributeGroup' => $this->attributeGroup,
            'htmlSelectGroup' => $htmlSelectGroup,
            'htmlSelectBuild' => $htmlSelectBuild,
            'listProductSingle' => $listProductSingle,
            'htmlProductAtrribute' => $htmlProductAtrribute,
            'htmlMoreImage' => $htmlMoreImage,
        ];

        return view('admin.screen.product_add')
            ->with($data);
    }

/**
 * Post create new order in admin
 * @return [type] [description]
 */

    public function postCreate()
    {
        
        $data = request()->all();
        $langFirst = array_key_first(sc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['name'];
        $data['alias'] = sc_word_format_url($data['alias']);
        $data['alias'] = sc_word_limit($data['alias'], 100);

        $double = ShopProduct::where('alias', $data['alias'])->first();
        if($double){
            $data['alias'] .=  '_' . time();
        }

        switch ($data['kind']) {
            case SC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'kind' => 'required',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:100',
                    'descriptions.*.keyword' => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'descriptions.*.content' => 'nullable|string', //was required
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku',
                    'alias' => 'regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias|string|max:100',//was required
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'descriptions.*.content.required' => trans('validation.required', ['attribute' => trans('product.content')]),
                    'category.required' => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                ];
                break;

            case SC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'kind' => 'required',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:100',
                    'descriptions.*.keyword' => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku',
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias|string|max:100',
                    'productBuild' => 'required',
                    'productBuildQty' => 'required',

                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'category.required' => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                ];
                break;

            case SC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'kind' => 'required',
                    'productInGroup' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku',
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias|string|max:100',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                ];
                break;

            default:
                $arrValidation = [
                    'kind' => 'required',
                ];
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }

        $category = $data['category'] ?? [];
        $attribute = $data['attribute'] ?? [];
        $descriptions = $data['descriptions'];
        $productInGroup = $data['productInGroup'] ?? [];
        $productBuild = $data['productBuild'] ?? [];
        $productBuildQty = $data['productBuildQty'] ?? [];
        $subImages = $data['sub_image'] ?? [];
        $supplier_id = $data['supplier_id']?? [];
        $dataInsert = [
            'brand_id' => $data['brand_id']??0,
            'supplier_id' => implode(',', $supplier_id ),
            'price' => $data['price']??0,
            'lower_price' => $data['lower_price']??0,
            'sku' => $data['sku'],
            'cost' => $data['cost']??0,
            'stock' => $data['stock']??0,
            'type' => $data['type'] ?? SC_PRODUCT_NORMAL,
            'kind' => $data['kind']??SC_PRODUCT_SINGLE,
            'alias' => $data['alias'],
            'virtual' => $data['virtual'] ?? SC_VIRTUAL_PHYSICAL,
            'date_available' => !empty($data['date_available']) ? $data['date_available'] : null,
            'image' => $data['image']??'',
            'tax_id' => $data['tax_id']??0,
            'status' => (!empty($data['status']) ? 1 : 0),
            'sort' => (int) $data['sort'],
        ];
        //insert product
        $product = ShopProduct::create($dataInsert);

        //Promoton price
        if (isset($data['price_promotion']) && in_array($data['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['price_promotion_start'] ? $data['price_promotion_start'] : null;
            $arrPromotion['date_end'] = $data['price_promotion_end'] ? $data['price_promotion_end'] : null;
            $product->promotionPrice()->create($arrPromotion);
        }

        //Insert category
        if ($category && in_array($data['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $product->categories()->attach($category);
        }
        //Insert group
        if ($productInGroup && $data['kind'] == SC_PRODUCT_GROUP) {
            $arrDataGroup = [];
            foreach ($productInGroup as $pID) {
                if ((int) $pID) {
                    $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                }
            }
            $product->groups()->saveMany($arrDataGroup);
        }

        //Insert Build
        if ($productBuild && $data['kind'] == SC_PRODUCT_BUILD) {
            $arrDataBuild = [];
            foreach ($productBuild as $key => $pID) {
                if ((int) $pID) {
                    $arrDataBuild[$pID] = new ShopProductBuild(['product_id' => $pID, 'quantity' => $productBuildQty[$key]]);
                }
            }
            $product->builds()->saveMany($arrDataBuild);
        }

        //Insert attribute
        if ($attribute && $data['kind'] == SC_PRODUCT_SINGLE) {
            $arrDataAtt = [];
            foreach ($attribute as $group => $rowGroup) {
                if (count($rowGroup)) {
                    foreach ($rowGroup as $key => $nameAtt) {
                        if ($nameAtt) {
                            $arrDataAtt[] = new ShopProductAttribute(['name' => $nameAtt, 'attribute_group_id' => $group]);
                        }
                    }
                }

            }
            $product->attributes()->saveMany($arrDataAtt);
        }

        //Insert description
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'product_id' => $product->id,
                'lang' => $code,
                'name' => $descriptions[$code]['name'],
                'keyword' => $descriptions[$code]['keyword'],
                'description' => $descriptions[$code]['description'],
                'content' => $descriptions[$code]['content'] ?? '',
            ];
        }

        ShopProductDescription::insert($dataDes);

        //Insert sub mages
        if ($subImages && in_array($data['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $arrSubImages = [];
            foreach ($subImages as $key => $image) {
                if ($image) {
                    $arrSubImages[] = new ShopProductImage(['image' => $image]);
                }
            }
            $product->images()->saveMany($arrSubImages);
        }

        return redirect()->route('admin_product.index')->with('success', trans('product.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $product = ShopProduct::find($id);

        if ($product === null) {
            return 'no data';
        }

        $listProductSingle = (new ShopProduct)->getProductSingle()->getData(['keyBy' => 'id'])->toArray();

        // html select product group
        $htmlSelectGroup = '<div class="select-product">';
        $htmlSelectGroup .= '<table width="100%"><tr><td width="80%"><select class="form-control productInGroup select2" data-placeholder="' . trans('product.admin.select_product_in_group') . '" style="width: 100%;" name="productInGroup[]" >';
        $htmlSelectGroup .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectGroup .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectGroup .= '</select></td><td><span title="Remove" class="btn btn-flat btn-danger removeproductInGroup"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectGroup .= '</div>';
        //End select product group

        // html select product build
        $htmlSelectBuild = '<div class="select-product">';
        $htmlSelectBuild .= '<table width="100%"><tr><td width="70%"><select class="form-control productInGroup select2" data-placeholder="' . trans('product.admin.select_product_in_build') . '" style="width: 100%;" name="productBuild[]" >';
        $htmlSelectBuild .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectBuild .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectBuild .= '</select></td><td style="width:100px"><input class="form-control"  type="number" name="productBuildQty[]" value="1" min=1></td><td><span title="Remove" class="btn btn-flat btn-danger removeproductBuild"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectBuild .= '</div>';
        //end select product build

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][]" value="attribute_value" class="form-control input-sm" placeholder="' . trans('product.admin.add_attribute_place') . '" /></td><td><br><span title="Remove" class="btn btn-flat btn-sm btn-danger removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        $data = [
            'title' => trans('product.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-pencil-square-o',
            'languages' => $this->languages,
            'product' => $product,
            'categories' => (new ShopCategory)->getTreeCategories(),
            'brands' => (new ShopBrand)->getList(),
            'suppliers' => (new ShopSupplier)->getList(),
            'taxs' => (new ShopTax)->getList(),
            'types' => $this->types,
            'virtuals' => $this->virtuals,
            'kinds' => $this->kinds,
            'attributeGroup' => $this->attributeGroup,
            'htmlSelectGroup' => $htmlSelectGroup,
            'htmlSelectBuild' => $htmlSelectBuild,
            'listProductSingle' => $listProductSingle,
            'htmlProductAtrribute' => $htmlProductAtrribute,
        ];
        return view('admin.screen.product_edit')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $product = ShopProduct::find($id);
        $data = request()->all();
        $langFirst = array_key_first(sc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['name'];
        $data['alias'] = sc_word_format_url($data['alias']);
        $data['alias'] = sc_word_limit($data['alias'], 100);

        switch ($product['kind']) {
            case SC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                    'descriptions.*.content' => 'nullable|string',//was required
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku,' . $product->id . ',id',
                    'alias' => 'regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias,' . $product->id . ',id|string|max:100', //was required
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'descriptions.*.content.required' => trans('validation.required', ['attribute' => trans('product.content')]),
                    'category.required' => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                ];
                break;
            case SC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku,' . $product->id . ',id',
                    'alias' => 'regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias,' . $product->id . ',id|string|max:100',//was required
                    'productBuild' => 'required',
                    'productBuildQty' => 'required',
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                    'category.required' => trans('validation.required', ['attribute' => trans('product.category')]),
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                ];
                break;

            case SC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,sku,' . $product->id . ',id',
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:'.SC_DB_PREFIX.'shop_product,alias,' . $product->id . ',id|string|max:100',
                    'productInGroup' => 'required',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:300',
                ];
                $arrMsg = [
                    'sku.regex' => trans('product.sku_validate'),
                    'alias.regex' => trans('product.alias_validate'),
                    'descriptions.*.name.required' => trans('validation.required', ['attribute' => trans('product.name')]),
                ];
                break;

            default:
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
//Edit

        $category = $data['category'] ?? [];
        $attribute = $data['attribute'] ?? [];
        $productInGroup = $data['productInGroup'] ?? [];
        $productBuild = $data['productBuild'] ?? [];
        $productBuildQty = $data['productBuildQty'] ?? [];
        $subImages = $data['sub_image'] ?? [];
        $supplier_id = $data['supplier_id']?? [];
        $dataUpdate = [
            'image' => $data['image'] ?? '',
            'tax_id' => $data['tax_id'] ?? 0,
            'brand_id' => $data['brand_id'] ?? 0,
            'supplier_id' => implode(',', $supplier_id  ),
            'price' => $data['price'] ?? 0,
            'lower_price' => $data['lower_price']??0,
            'cost' => $data['cost'] ?? 0,
            'stock' => $data['stock'] ?? 0,
            'type' => $data['type'] ?? SC_PRODUCT_NORMAL,
            'virtual' => $data['virtual'] ?? SC_VIRTUAL_PHYSICAL,
            'date_available' => !empty($data['date_available']) ? $data['date_available'] : null,
            'sku' => $data['sku'],
            'alias' => $data['alias'],
            'status' => (!empty($data['status']) ? 1 : 0),
            'sort' => (int) $data['sort'],
        ];

        $product->update($dataUpdate);

        //Promoton price
        $product->promotionPrice()->delete();
        if (isset($data['price_promotion']) && in_array($product['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['price_promotion_start'] ? $data['price_promotion_start'] : null;
            $arrPromotion['date_end'] = $data['price_promotion_end'] ? $data['price_promotion_end'] : null;
            $product->promotionPrice()->create($arrPromotion);
        }

        $product->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'product_id' => $id,
                'lang' => $code,
                'name' => $row['name'],
                'keyword' => $row['keyword'],
                'description' => $row['description'],
                'content' => $row['content'] ?? '',
            ];
        }
        ShopProductDescription::insert($dataDes);

        //Update category
        if (in_array($product['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $product->categories()->detach();
            if (count($category)) {
                $product->categories()->attach($category);
            }

        }

        //Update group
        if ($product['kind'] == SC_PRODUCT_GROUP) {
            $product->groups()->delete();
            if (count($productInGroup)) {
                $arrDataGroup = [];
                foreach ($productInGroup as $pID) {
                    if ((int) $pID) {
                        $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                    }
                }
                $product->groups()->saveMany($arrDataGroup);
            }

        }

        //Update Build
        if ($product['kind'] == SC_PRODUCT_BUILD) {
            $product->builds()->delete();
            if (count($productBuild)) {
                $arrDataBuild = [];
                foreach ($productBuild as $key => $pID) {
                    if ((int) $pID) {
                        $arrDataBuild[$pID] = new ShopProductBuild(['product_id' => $pID, 'quantity' => $productBuildQty[$key]]);
                    }
                }
                $product->builds()->saveMany($arrDataBuild);
            }

        }

        //Update attribute
        if ($product['kind'] == SC_PRODUCT_SINGLE) {
            $product->attributes()->delete();
            if (count($attribute)) {
                $arrDataAtt = [];
                foreach ($attribute as $group => $rowGroup) {
                    if (count($rowGroup)) {
                        foreach ($rowGroup as $key => $nameAtt) {
                            if ($nameAtt) {
                                $arrDataAtt[] = new ShopProductAttribute(['name' => $nameAtt, 'attribute_group_id' => $group]);
                            }
                        }
                    }

                }
                $product->attributes()->saveMany($arrDataAtt);
            }

        }

        //Update sub mages
        if ($subImages && in_array($product['kind'], [SC_PRODUCT_SINGLE, SC_PRODUCT_BUILD])) {
            $product->images()->delete();
            $arrSubImages = [];
            foreach ($subImages as $key => $image) {
                if ($image) {
                    $arrSubImages[] = new ShopProductImage(['image' => $image]);
                }
            }
            $product->images()->saveMany($arrSubImages);
        }

//
        return redirect()->route('admin_product.index')->with('success', trans('product.admin.edit_success'));

    }

/*
Delete list Item
Need mothod destroy to boot deleting in model
 */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrCantDelete = [];
            foreach ($arrID as $key => $id) {
                if (ShopProductBuild::where('product_id', $id)->first() || ShopProductGroup::where('product_id', $id)->first()) {
                    $arrCantDelete[] = $id;}
            }
            if (count($arrCantDelete)) {
                return response()->json(['error' => 1, 'msg' => trans('product.admin.cant_remove_child') . ': ' . json_encode($arrCantDelete)]);
            } else {
                ShopProduct::destroy($arrID);
                return response()->json(['error' => 0, 'msg' => '']);
            }

        }
    }

}
