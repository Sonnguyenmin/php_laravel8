@extends('AdminLayout')
@section('admin_content')

<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h3>Sản phẩm</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin')}}"><i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item">Sản phẩm</li>
                    <li class="breadcrumb-item active">Sửa Sản phẩm</li>
                </ol>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0" style="display:flex; justify-content: space-between;">
                        <h3>Thêm Sản Phẩm</h3>
                        <span style=" margin-top: 3px;">
                              @include('Backend.admin.alert')
                        </span>
                    </div>
                    <form class="form theme-form" action="{{route('product.update',$product->id)}}" method="Post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Tên thương hiệu</label>
                                        <div class="col-sm-9">
                                            <select name="brand_id" class="form-select select_brand">
                                                @foreach ($brand as $key => $br)
                                                    <option value="{{$br->id}}">{{$br->brand_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Chọn danh mục:</label>
                                        <div class="col-sm-9">
                                            <select class="form-select digits select_2" name="category_id" >
                                                <option value= "">danh mục: </option>
                                                {!!$htmlOption!!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label">Tên sản phẩm</label>
                                        <div class="col-sm-9">
                                            <input class="form-control"
                                            type="text"
                                            id="slug"
                                            onkeyup="ChangeToSlug()"
                                            name="pro_name"
                                            value="{{$product->pro_name}}">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Slug</label>
                                        <div class="col-sm-9">
                                        <input class="form-control"
                                        id="convert_slug"
                                        type="text"
                                        name="slug"
                                        value="{{$product->slug}}">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Giá tiền</label>
                                        <div class="col-sm-9">
                                        <input class="form-control"
                                        type="text"
                                        name="pro_price"
                                        value="{{$product->pro_price}}">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="col-sm-3 col-form-label pt-0">Màu sắc</label>
                                                <div >
                                                    @foreach ($color as $key=> $value)
                                                        <input style="font-size: 20px"  class="form-check-input"
                                                        type="checkbox"
                                                        value="{{$value->id}}"
                                                        name="id_attr[]" {{(in_array($value->id, $id_attr)?'checked':'')}}>
                                                        <i data-feather="shopping-bag"
                                                        style="margin-right: 10px; position: relative; top: 2px; color:{{$value->attr_value}}"></i>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kích thước</label>
                                                <div >
                                                    @foreach ($size as $key=> $value)
                                                        <input style="font-size: 20px"  class="form-check-input"
                                                        type="checkbox"
                                                        value="{{$value->id}}"
                                                        name="id_attr[]"
                                                        {{(in_array($value->id, $id_attr)?'checked':'')}}>
                                                        <span style="font-size: 16px; margin-right: 6px; position: relative; top: 3px;">{{$value->attr_value}} </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Hình ảnh</label>
                                        <div class="col-sm-9">
                                            <input class="form-control"  type="file"  name="feature_image">
                                        </div>
                                        <label class="col-sm-3 col-form-label"></label>
                                        <div class="col-sm-9">
                                            <img src="{{$product->feature_image}}" alt="" style="width: 100px; height:100px; border-radius: 5px; margin-top: 10px">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Ảnh chi tiết</label>
                                        <div class="col-sm-9">
                                        <input class="form-control"  type="file" multiple  name="image_path[]">
                                        </div>
                                        <label class="col-sm-3 col-form-label"></label>
                                        @foreach ($product->proImages as $ProI)

                                                <img src="{{$ProI->image_path}}" alt="" style="width: 100px; height:100px; border-radius: 5px; margin:10px 10px 0 10px; padding:0px">

                                        @endforeach

                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Nhập tag cho sản phẩm:</label>
                                        <div class="col-sm-9" >
                                            <select name="tags[]" class="form-control tag_select" multiple="multiple">
                                                @foreach ($product->tags as $tagItem)
                                                    <option value="{{$tagItem->name}}" selected>{{$tagItem->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3 col-form-label">Nội dung</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control tinymce_edit"
                                            style="resize: none"
                                            rows="6"
                                            cols="6"
                                            name="pro_content">{{$product->pro_content}}</textarea>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label">Trạng thái</label>
                                        <div class="col-sm-9">
                                            <select class="form-select digits" name="pro_status" >
                                                @if($product->pro_status == 1)
                                                    <option value= "0">Ẩn </option>
                                                    <option value="1" selected>Hiển thị</option>
                                                @else
                                                    <option value= "0" selected >Ẩn </option>
                                                    <option value="1">Hiển thị</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="col-sm-9 offset-sm-3">
                                <button class="btn btn-primary" type="submit">Thêm</button>
                                <a href="{{route('product.index')}}">
                                    <input class="btn btn-light" style="width: 136px;" value="Xem chi tiết">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
