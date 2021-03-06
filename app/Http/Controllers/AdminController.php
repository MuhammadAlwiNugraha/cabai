<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Komoditas;
use App\PesananDetail;
use App\Pesenan;
use App\Inventory;
use App\BarangMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Intervention\Image\ImageManagerStatic as Image;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use RegistersUsers;

    public function coba(Request $request)
    {
        $totals = Pesenan::All();


        return view('admin.coba', compact('totals'));
    }

    public function registertoko()
    {
        return view('admin.registertoko');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function storetoko()
    {


        $request = app('request');
        if($request->hasfile('ktp')){
            $ktp = $request->file('ktp');
            $filename = time() . '.' . $ktp->getClientOriginalExtension();
            Image::make($ktp)->save( public_path('/ktp/' . $filename) );
        };
        if($request->hasfile('avatar')){
            $avatar = $request->file('avatar');
            $filename2 = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->save( public_path('/avatar/' . $filename2) );
        };

        $role = 2;
        $status = 1;
    

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
            'ktp' => $filename,
            'avatar' => $filename2,
            'role' => $role,
            'status_id' => $status,
        ]);

        $komoditas = DB::table("komoditas")->get();
        $toko = DB::table("users")->where('email', $request->email)->first();
        foreach($komoditas as $komoditi)
        {
            $pesanan = new Pesenan();
            $pesanan->user_id = 1;
            $pesanan->toko_id = $toko->id;
            $pesanan->tanggal = Carbon::now();
            $pesanan->status = 2;
            $pesanan->jumlah_harga = 0;
            $pesanan->tanggal_ambil = Carbon::now();
            $pesanan->kode_transaksi = 1199;
            $pesanan->ongkir = 0;

            $pesanan->save();

            $inventory = new Inventory();
            $inventory->komoditas_id = $komoditi->id_komoditas;
            $inventory->toko_id = $toko->id;
            $inventory->qty_stok = 0;
            $inventory->created_by = Auth::user()->id;
            $inventory->tanggal = Carbon::now();

            $inventory->save();

            $barangmasuk = new BarangMasuk();
            $barangmasuk->komoditas_id = $komoditi->id_komoditas;
            $barangmasuk->qty_barangmasuk = 0;
            $barangmasuk->tgl_masuk = Carbon::now();
            $barangmasuk->user_id = Auth::user()->id;
            $barangmasuk->toko_id = $toko->id;

            $barangmasuk->save();
        }



        return redirect('admin/toko');

    }

    public function indexpembeli()
    {

        $widget = User::latest()->get();
        $users = User::latest()->where('role', 3)->paginate(2);
  
        return view('admin.pembeli',compact('users','widget'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexgrafik()
    {
        
        return view('admin.grafik');
    }


    public function indexkomoditi()
    {
        $komoditas = Komoditas::latest()->paginate(3);
        $stoks = DB::table('v_stokupdate')->get();
  
        return view('admin.komoditas',compact('komoditas', 'stoks'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function aktivasipembeli($id)
    {
        $pembeli = User::where('id', $id)->first();
        $pembeli->status_id = 1;
        $pembeli->update();

        return redirect('admin/pembeli');
    }

    public function nonaktifpembeli($id)
    {
        $pembeli = User::where('id', $id)->first();
        $pembeli->status_id = 0;
        $pembeli->update();

        return redirect('admin/pembeli');
    }

    

    public function createkomoditi()
    {
        return view('admin.komoditas');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storekomoditi(Request $request)
    {
        $komoditas =  new Komoditas();

        $komoditas->nama_komoditas = $request->input('nama_komoditas');
        $komoditas->jenis = $request->input('jenis');
        $komoditas->harga = $request->input('harga');

        if($request->hasfile('img_komoditas')) {
            $file = $request->file('img_komoditas');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('img_komoditas/', $filename);
            $komoditas->img_komoditas = $filename;
        } else {
            return $request;
            $komoditas->image = '';
        }

        $komoditas->save();

        $toko = DB::table("users")->where('role', 2)->get();
        foreach($toko as $tokos)
        {
            $pesanan = new Pesenan();
            $pesanan->user_id = 1;
            $pesanan->toko_id = $tokos->id;
            $pesanan->tanggal = Carbon::now();
            $pesanan->status = 2;
            $pesanan->jumlah_harga = 0;
            $pesanan->tanggal_ambil = Carbon::now();
            $pesanan->kode_transaksi = 1199;
            $pesanan->ongkir = 0;

            $pesanan->save();

            $inventory = new Inventory();
            $inventory->komoditas_id = $komoditas->id_komoditas;
            $inventory->toko_id = $tokos->id;
            $inventory->qty_stok = 0;
            $inventory->created_by = Auth::user()->id;
            $inventory->tanggal = Carbon::now();

            $inventory->save();

            $barangmasuk = new BarangMasuk();
            $barangmasuk->komoditas_id = $komoditas->id_komoditas;
            $barangmasuk->qty_barangmasuk = 0;
            $barangmasuk->tgl_masuk = Carbon::now();
            $barangmasuk->user_id = Auth::user()->id;
            $barangmasuk->toko_id = $tokos->id;

            $barangmasuk->save();
        }

        return redirect('/admin/pembeli');
    }

    public function editkomoditi($id)
    {
        $komoditas = DB::table("komoditas")->where('id_komoditas', $id)->first();
  
        return view('admin.komoditasedit')->with('komoditas', $komoditas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatekomoditi(Request $request, $id)
    {
        DB::table('komoditas')->where('id_komoditas', $id)->update([
            'nama_komoditas' => $request->nama_komoditas,
            'jenis' => $request->jenis,
            'harga' => $request->harga,
        ]);

        return redirect('/admin/komoditas');

    }


    public function indexpesanan()
    {
         $pesanans = Pesenan::Latest()->orderBy('tanggal', 'asc')->paginate(5);

  
        return view('admin.indexpesanan',compact('pesanans'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function pesanandetail($id)
    {
        $pesanans = Pesenan::Latest()->orderBy('tanggal', 'asc')->paginate(5);
        $pesanan = Pesenan::where('id', $id)->first();
        $pesanan_details = PesananDetail::where('pesanan_id', $pesanan->id)->get();

        return view('admin.detailpesanan', compact('pesanans','pesanan', 'pesanan_details'));
    }

    public function filterpesanan(Request $request)
    {
        $pesanan = Pesenan::Latest();

        $status = $request->status;
        $pesanans = Pesenan::where('status', $status)->orderBy('tanggal', 'asc')->paginate(5);

        return view('admin.indexpesanan', compact('pesanan','pesanans'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroykomoditi($id)
    {
        $komoditas = DB::table('komoditas')->where('id_komoditas', $id)->delete();
        return redirect('/admin/komoditas');
    }

    public function ongkir()
    {
        $ongkirs = DB::table('ongkir')->get();

        return view('admin.ongkir', compact('ongkirs'));
    }

    public function buatongkir()
    {
        return view('admin.buatongkir');
    }

    public function storeongkir(Request $request)
    {
        DB::table('ongkir')->insert([
            'status' => $request->status,
             'ongkir' => $request->ongkir,
             'keterangan' => $request->keterangan
        ]);

        return redirect('admin/ongkir');
    }

    public function editongkir($id)
    {

        $ongkirs = DB::table('ongkir')->where('id', $id)->first();

        return view('admin.editongkir', compact('ongkirs'));
    }

    public function updateongkir(Request $request, $id)
    {

         DB::table('ongkir')->where('id', $id)->update([
            'status' => $request->status,
            'ongkir' => $request->ongkir,
            'keterangan' => $request->keterangan,
        ]);
        return redirect('admin/ongkir');
    }

    public function destroyongkir($id)
    {
        $ongkir = DB::table('ongkir')->where('id', $id)->delete();
        return redirect('admin/ongkir');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete pembeli
        $user = User::find($id);
        $user->delete();
        return redirect('/admin')->with('success', 'Contact deleted!');
    }


    public function indextoko()
    {
        $tokos = User::where('role', 2)->get();
        return view('admin.toko', compact('tokos'));
    }
}
