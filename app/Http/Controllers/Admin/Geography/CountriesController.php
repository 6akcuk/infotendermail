<?php namespace App\Http\Controllers\Admin\Geography;

use App\Commands\Geography\CreateCountryCommand;
use App\Commands\Geography\UpdateCountryCommand;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Geography\CountryRequest;
use App\Models\Geography\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller {

	public function __construct() {
		$this->loadAndAuthorizeResource(['class' => 'App\Models\Geography\Country']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$countries = Country::paginate(20);

		return view('admin.countries.index', compact('countries'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.countries.create');
	}

  /**
   * Store a newly created resource in storage.
   *
   * @param CountryRequest $request
   * @return Response
   */
	public function store(CountryRequest $request)
	{
		Country::create([
			'name' => $request->name,
			'phonecode' => $request->phonecode
		]);

		flash()->success('Страна успешно добавлена.');
		return redirect()->route('admin.countries.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$country = Country::findOrFail($id);

    	return view('admin.countries.edit', compact('country'));
	}

  /**
   * Update the specified resource in storage.
   *
   * @param  int $id
   * @param CountryRequest $request
   * @return Response
   */
	public function update($id, CountryRequest $request)
	{
		$country = Country::findOrFail($id);
		$country->update([
			'name' => $request->name,
			'phonecode' => $request->phonecode
		]);

		flash()->success('Изменения успешно сохранены.');
		return redirect()->route('admin.countries.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$country = Country::findOrFail($id);
		$country->delete();

		flash()->success('Страна удалена.');
		return redirect()->route('admin.countries.index');
	}

}
