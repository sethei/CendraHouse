<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Models\Type, App\Http\Models\Property, App\Http\Models\PGallery;



use Validator, Str, Config, Image;

class PropertyController extends Controller
{
    public function __Construct(){
        $this->middleware('auth');
        //Amb aquest middleware sino es admin no podrà accedir
        $this->middleware('isadmin');
    }

    public function getHome(){
        //Si canviem el numero de paginate podem mostrar que hi ha paginació
        $properties = Property::with(['cat'])->orderBy('id', 'desc')->paginate(25);
        $data = ['properties' => $properties];
        return view('admin.properties.home', $data);
    }

    public function getPropertyAdd(){
        $cats = Type::where('module', '0')->pluck('name', 'id');
        $data = ['cats' => $cats];
        return view('admin.properties.add', $data);
    }

    public function postPropertyAdd(Request $request){
        $rules = [
            'name' => 'required',
            'n_rooms' => 'required',
            'n_baths' => 'required',
            'img' => 'required',
            'price' => 'required',
            'content' => 'required'
        ];

        $messages = [
            'name.required' => 'El nom de la propietat és obligatori',
            'n_rooms.required' => 'El número d´habitacions és obligatori',
            'n_baths.required' => 'El número de banys és obligatori',
            'img.required' => 'La imatge és obligatòria',
            'img.image' => 'L´arxiu no és una imatge',
            'price.required' => 'El preu és obligatori',
            'content.required' => 'La descripció és obligatòria'
        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()):
            return back()->withErrors($validator)->with('message', 'S´ha produit un error')->with('typealert', 'danger')->withInput();
        else:
            $path = '/'.date('Y-m-d'); // 2020-05-20 Per exemple
            $fileExt = trim($request->file('img')->getClientOriginalExtension());
            $upload_path = Config::get('filesystems.disks.uploads.root');
            //Amb slug eliminem els espais i caracters especials del nom de l'arxiu
            $name = Str::slug(str_replace($fileExt, '', $request->file('img')->getClientOriginalName()));
            //amb aquesta linia fem que si pujo un altre arxiu amb el mateix nom no el substitueix ja que li posa un nom aleatori, com a més es guarda en una carpeta diferent
            //per cada dia hi han menys probabilitats de que falli
            $filename = rand(1,999).'-'.$name.'.'.$fileExt;
            $file_file = $upload_path.'/'.$path.'/'.$filename;

            $property = new Property;
            //Si la propietat està posada en 0 és un borrador i si es 1 està publicada
            $property->status = '1';
            $property->name = e($request->input('name'));
            $property->slug = Str::slug($request->input('name'));
            $property->n_rooms = $request->input('n_rooms');
            $property->n_baths = $request->input('n_baths');
            $property->type_id = $request->input('type');
            $property->file_path = date('Y-m-d');
            $property->image = $filename;
            $property->price = $request->input('price');
            $property->in_discount = $request->input('indiscount');
            $property->discount = $request->input('discount');
            $property->content = e($request->input('content'));
            //Guardem la imatge
            if($property->save()):
                if($request->hasFile('img')):
                    //Uploads està creat a filesystems.php
                    $fl = $request->img->storeAs($path, $filename, 'uploads');
                    $img = Image::make($file_file);
                    //Part per si s'ha de modificar la mida
                    $img->fit(256, 256, function($constraint){
                        //t_ = thumbnail
                        $constraint->upsize();
                    });
                    $img->save($upload_path.'/'.$path.'/t_'.$filename);
                endif;
                return redirect('/admin/properties')->with('message', 'S´ha guardat correctament')->with('typealert', 'success');
            endif;
        endif;
    }

    public function getPropertyEdit($id){
        $p = Property::findOrFail($id);
        $cats = Type::where('module', '0')->pluck('name', 'id');
        $data = ['cats' => $cats, 'p' => $p];
        return view('admin.properties.edit', $data);
    }
    public function postPropertyEdit($id, Request $request){
        $rules = [
            'name' => 'required',
            'n_rooms' => 'required',
            'n_baths' => 'required',
            'price' => 'required',
            'content' => 'required'
        ];

        $messages = [
            'name.required' => 'El nom de la propietat és obligatori',
            'n_rooms.required' => 'El número d´habitacions és obligatori',
            'n_baths.required' => 'El número de banys és obligatori',
            'img.image' => 'L´arxiu no és una imatge',
            'price.required' => 'El preu és obligatori',
            'content.required' => 'La descripció és obligatòria'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()):
            return back()->withErrors($validator)->with('message', 'S´ha produit un error')->with('typealert', 'danger')->withInput();
        else:
            $property = Property::findOrFail($id);
            //ipp image preview path
            $ipp = $property->file_path;
            $ip = $property->image;
            //Si la propietat està posada en 0 és un borrador i si es 1 està publicada
            $property->status = $request->input('status');
            $property->name = e($request->input('name'));
            $property->n_rooms = $request->input('n_rooms');
            $property->n_baths = $request->input('n_baths');
            $property->type_id = $request->input('type');
            if($request->hasFile('img')):
                $path = '/'.date('Y-m-d'); // 2020-05-20 Per exemple
                $fileExt = trim($request->file('img')->getClientOriginalExtension());
                $upload_path = Config::get('filesystems.disks.uploads.root');
                //Amb slug eliminem els espais i caracters especials del nom de l'arxiu
                $name = Str::slug(str_replace($fileExt, '', $request->file('img')->getClientOriginalName()));
                //amb aquesta linia fem que si pujo un altre arxiu amb el mateix nom no el substitueix ja que li posa un nom aleatori, com a més es guarda en una carpeta diferent
                //per cada dia hi han menys probabilitats de que falli
                $filename = rand(1,999).'-'.$name.'.'.$fileExt;
                $file_file = $upload_path.'/'.$path.'/'.$filename;
                $property->file_path = date('Y-m-d');
                $property->image = $filename;
            endif;
            $property->price = $request->input('price');
            $property->in_discount = $request->input('indiscount');
            $property->discount = $request->input('discount');
            $property->content = e($request->input('content'));
            //Guardem la imatge
            if($property->save()):
                if($request->hasFile('img')):
                    //Uploads està creat a filesystems.php
                    $fl = $request->img->storeAs($path, $filename, 'uploads');
                    $img = Image::make($file_file);
                    //Part per si s'ha de modificar la mida
                    $img->fit(256, 256, function($constraint){
                        //t_ = thumbnail
                        $constraint->upsize();
                    });
                    $img->save($upload_path.'/'.$path.'/t_'.$filename);
                    unlink($upload_path.'/'.$ipp.'/'.$ip);
                    unlink($upload_path.'/'.$ipp.'/t_'.$ip);
                endif;
                return back()->with('message', 'S´ha actualitzat correctament')->with('typealert', 'success');
            endif;
        endif;
    }

    public function getPropertyDelete($id){
        $p = Property::find($id);
        if($p->delete()):
            return back()->with('message', 'S´ha eliminat correctament')->with('typealert', 'success');
        endif;
    }

    public function postPropertyGalleryAdd($id, Request $request){
        $rules = [
            'file_image' => 'required'
        ];

        $messages = [
            'file_image.required' => 'La imatge és obligatòria'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()):
            return back()->withErrors($validator)->with('message', 'S´ha produit un error')->with('typealert', 'danger')->withInput();
        else:
            if($request->hasFile('file_image')):
                $path = '/'.date('Y-m-d'); 
                $fileExt = trim($request->file('file_image')->getClientOriginalExtension());
                $upload_path = Config::get('filesystems.disks.uploads.root');
                $name = Str::slug(str_replace($fileExt, '', $request->file('file_image')->getClientOriginalName()));

                $filename = rand(1,999).'-'.$name.'.'.$fileExt;
                $file_file = $upload_path.'/'.$path.'/'.$filename;

                $g = new PGallery;
                $g->property_id = $id;
                $g->file_path = date('Y-m-d');
                $g->file_name = $filename;

                if($g->save()):
                    if($request->hasFile('file_image')):
                        //Uploads està creat a filesystems.php
                        $fl = $request->file_image->storeAs($path, $filename, 'uploads');
                        $img = Image::make($file_file);
                        //Part per si s'ha de modificar la mida
                        $img->fit(256, 256, function($constraint){
                            //t_ = thumbnail
                            $constraint->upsize();
                        });
                        $img->save($upload_path.'/'.$path.'/t_'.$filename);
                    endif;
                    return back()->with('message', 'S´ha pujat correctament')->with('typealert', 'success');
                endif;

            endif;
        endif;
    }

    function getPropertyGalleryDelete($id, $gid){
        $g = PGallery::findOrFail($gid);
        $path = $g->file_path;
        $file = $g->file_name;
        $upload_path = Config::get('filesystems.disks.uploads.root');
        if($g->property_id != $id){
            return back()->with('message', 'Aquesta imatge no es pot eliminar')->with('typealert', 'danger');
        }else{
            if($g->delete()):
                unlink($upload_path.'/'.$path.'/'.$file);
                unlink($upload_path.'/'.$path.'/t_'.$file);
                return back()->with('message', 'S´ha eliminat correctament')->with('typealert', 'success');
            endif;
        }
    }
}
