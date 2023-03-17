<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Components\Recursive;
use App\Models\ProAttr;
use App\Traits\StorageImageTraits;
use App\Models\Brand;
use App\Models\ProImage;
use App\Models\Tag;
use App\Models\ProTag;

class ProductController extends Controller
{
    use StorageImageTraits;
    private $category;
    private $brand;
    private $product;
    private $proAttr;
    private $proImage;
    private $tag;
    private $proTag;


    public function __construct(Category $category, Brand $brand, Product $product,ProAttr $proAttr, ProImage $proImage, Tag $tag , ProTag $proTag)
    {
        $this->category = $category;
        $this->brand = $brand;
        $this->product = $product;
        $this->proAttr = $proAttr;
        $this->proImage = $proImage;
        $this->tag = $tag;
        $this->proTag = $proTag;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('id','DESC')->latest()->get();//lấy cái mới nhất
        return view('Backend.pages.Product.index_pro', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $htmlOption = $this->getCategory($parentId ="");
        $color = Attribute::where('attr_name','color')->get();
        $size = Attribute::where('attr_name','size')->get();
        $brand = Brand::orderBy('id','DESC')->get();
        return view('Backend.pages.Product.create_pro', compact('htmlOption','color','size', 'brand'));
    }

    public function getCategory($parentId){//function dùng chung
        $data = $this->category->all();
        $recusive = new Recursive($data);
        $htmlOption = $recusive->categoryRecusive($parentId);
        return $htmlOption;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'pro_name' => 'required|unique:tbl_product|max:255',
                'feature_image' => 'required',
                'pro_content' => 'required',
                'slug' => 'required',
            ],
            [
                'pro_name.unique' => 'Tên sản phẩm đã có, xin điền tên khác',
                'pro_name.required' => 'Tên sản phẩm là bắt buộc',
                'pro_name.max' => 'Tên sản phẩm không vượt quá 255 kí tự',
                'feature_image' => 'Hình ảnh sản phẩm là bắt buộc',
                'slug.unique' => 'Slug sản phẩm đã có, xin điền Slug khác',
                'slug.required' => 'Slug sản phẩm là bắt buộc',
                'slug.max' => 'Slug sản phẩm không vượt quá 255 kí tự',
                'pro_content.required' => 'trạng thái là phải có nhé',
            ]
        );
        try {
            DB::beginTransaction();
            $dataProduct =[
                'user_id' => auth()->id(),
                'brand_id'=>$request->brand_id,
                'category_id'=>$request->category_id,
                'pro_name'=>$request->pro_name,
                'pro_price'=>$request->pro_price,
                'pro_content'=>$request->pro_content,
                'slug'=>$request->slug,
                'pro_status'=>$request->pro_status,
            ];
            $dataUploadImage = $this->storageTraitUpload($request,'feature_image','product');
            if(!empty($dataUploadImage)){
                $dataProduct['feature_image_name'] = $dataUploadImage['file_name'];
                $dataProduct['feature_image'] = $dataUploadImage['file_path'];
            }
            $product = $this->product->create($dataProduct);
            foreach($request->id_attr as $value ){
                $this->proAttr->create([
                    'id_product'=> $product->id,
                    'id_attr' =>$value
                ]);
            }
            //Insert data to product_image
            if($request->hasFile('image_path')){
                foreach($request->image_path as $fileItem){
                        $dataProductImageDetail = $this->storageTraitUploadMutiple($fileItem, 'product');
                        $product->images()->create([
                            'id_product' =>$product->id,
                            'image_path' => $dataProductImageDetail['file_path'],
                            'image_name' => $dataProductImageDetail['file_name'],
                        ]);
                }
            }
           //Insert tag for product
           if(!empty($request->tags)){
               foreach($request->tags as $tagItem){
                   $tagInstance = $this->tag->firstOrCreate([
                       'name' => $tagItem,
                   ]);
                   $tagIds[] = $tagInstance->id;
               }
               $product->tags()->attach($tagIds);
           }
            DB::commit();
            return redirect()->back()->with('success','Thêm thành công sản phẩm');

        }
        catch(\Exception $exception) {
            DB::rollBack();
            Log::error("Message:" . $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = $this->product->find($id);
        $htmlOption = $this->getCategory($product->category_id);
        $color = Attribute::where('attr_name','color')->get();
        $size = Attribute::where('attr_name','size')->get();
        $id_attr = DB::table('pro_attrs')->where('id_product', $id)->pluck('id_attr')->toArray();
        $brand = Brand::orderBy('id','DESC')->get();
        return view('Backend.pages.Product.edit_pro', compact('htmlOption','color','size', 'brand', 'product', 'id_attr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate(
            [
                'pro_name' => 'required|unique:tbl_product|max:255',
                'feature_image' => 'required',
                'pro_content' => 'required',
                'slug' => 'required',
            ],
            [
                'pro_name.unique' => 'Tên sản phẩm đã có, xin điền tên khác',
                'pro_name.required' => 'Tên sản phẩm là bắt buộc',
                'pro_name.max' => 'Tên sản phẩm không vượt quá 255 kí tự',
                'feature_image' => 'Hình ảnh sản phẩm là bắt buộc',
                'slug.unique' => 'Slug sản phẩm đã có, xin điền Slug khác',
                'slug.required' => 'Slug sản phẩm là bắt buộc',
                'slug.max' => 'Slug sản phẩm không vượt quá 255 kí tự',
                'pro_content.required' => 'trạng thái là phải có nhé',
            ]
        );
        try {
            DB::beginTransaction();
            $dataProductUpdate =[
                'user_id' => auth()->id(),
                'brand_id'=>$request->brand_id,
                'category_id'=>$request->category_id,
                'pro_name'=>$request->pro_name,
                'pro_price'=>$request->pro_price,
                'pro_content'=>$request->pro_content,
                'slug'=>$request->slug,
                'pro_status'=>$request->pro_status,
            ];
            $dataUploadImage = $this->storageTraitUpload($request,'feature_image','product');
            if(!empty($dataUploadImage)){
                $dataProductUpdate['feature_image_name'] = $dataUploadImage['file_name'];
                $dataProductUpdate['feature_image'] = $dataUploadImage['file_path'];
            }
            $this->product->find($id)->update($dataProductUpdate);
            $product = $this->product->find($id);
            $this->proAttr->where('id_product', $id)->delete();
            foreach($request->id_attr as $value ){
                $this->proAttr->create([
                    'id_product'=> $product->id,
                    'id_attr' =>$value
                ]);
            }
            //Insert data to product_image
            if($request->hasFile('image_path')){
                $this->proImage->where('id_product', $id)->delete();
                foreach($request->image_path as $fileItem){
                        $dataProductImageDetail = $this->storageTraitUploadMutiple($fileItem, 'product');
                        $product->images()->create([
                            'id_product' =>$product->id,
                            'image_path' => $dataProductImageDetail['file_path'],
                            'image_name' => $dataProductImageDetail['file_name'],
                        ]);
                }
            }
           //Insert tag for product
           if(!empty($request->tags)){
               foreach($request->tags as $tagItem){

                   $tagInstance = $this->tag->firstOrCreate([
                       'name' => $tagItem,
                   ]);
                   $tagIds[] = $tagInstance->id;
               }
               $product->tags()->attach($tagIds);
           }
            DB::commit();
            return redirect()->back()->with('success','Thêm thành công sản phẩm');

        }
        catch(\Exception $exception) {
            DB::rollBack();
            Log::error("Message:" . $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->back();
    }
}
