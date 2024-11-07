<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // It's show List of product page

        $products = Product::orderBy('created_at','DESC')->get();

        return view('products.list',['products' => $products]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // It's show create product page

        return view('products.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // It's save data in product table in database

        $rules = [
            'name' => 'required|min:3',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];

        if($request->image != ""){

            $rules['image'] = 'image';
            // dump($rules);
        }        

        $validator = Validator::make($request->all(), $rules);
        //dump($validator);

        if($validator->fails()){
            
            return redirect()->route('products.create')->withInput()->withErrors($validator);

        } else {

            // Now save data in product table of database

            $product = new Product();

            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->save();

            
            // Now save image in product table of database

            if($request->image != ""){               
            
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();

                $imageName = time().'.'.$ext; // unique name of image

                // save image to products folder 
                $image->move(public_path('uploads/products/'),$imageName);


                $product->image = $imageName;
                $product->save();
            }  
    
            return redirect()->route('products.index')->with('success','Product Added Successfully.');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //

        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product = Product::findOrFail($product->id); 

        return view('products.edit', [ 'product' => $product ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // It's update data in product table in database

        $product = Product::findOrFail($product->id); 

        $rules = [
            'name' => 'required|min:3',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
        ];

        if($request->image != ""){

            $rules['image'] = 'image';
            // dump($rules);
        }        

        $validator = Validator::make($request->all(), $rules);
        //dump($validator);

        if($validator->fails()){
            
            return redirect()->route('products.edit', $product->id )->withInput()->withErrors($validator);

        } else {

            // Now update data in product table of database

            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->save();
            
            // Now update image in product table of database

            if($request->image != ""){      
                
                // delete old image
                File::delete(public_path('uploads/products/'. $product->image));
            
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();

                $imageName = time().'.'.$ext; // unique name of image

                // update image to products folder 
                $image->move(public_path('uploads/products/'),$imageName);


                $product->image = $imageName;
                $product->save();
            }  
    
            return redirect()->route('products.index')->with('success','Product Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product = Product::findOrFail($product->id); 

        // delete old image
        File::delete(public_path('uploads/products/'. $product->image));

        // delete product
        $product->delete();
        return redirect()->route('products.index')->with('success','Product Deleted Successfully.');

    }
}
