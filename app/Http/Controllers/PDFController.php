<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Project;
use App\Http\Filters\IncomeFilters;
use App\Http\Filters\ExpenseFilters;
use Illuminate\Http\JsonResponse;
//use PDF;
use ArPHP\I18N\Arabic;
class PDFController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function generatePDF()

    {

        $users = User::get();

  

        $data = [

            'title' => 'Welcome to ItSolutionStuff.com',

            'date' => date('m/d/Y'),

            'users' => $users

        ]; 

            

        $pdf = PDF::loadView('welcome', $data);

     

        return $pdf->download('public\reports\itsolutionstuff.pdf');

    }
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function incomesPDF(IncomeFilters $incomeFilters)
    {
        $incomes = Income::with('project')->filterBy($incomeFilters)->get();

        $data = [
            'title'    => 'El-Saleh Income Report',
            'date'     => date('m/d/Y'),
            'incomes'  => $incomes,
        ];


	$html = view('income',compact('data'))->render();
	$arabic = new Arabic();
	$p = $arabic->arIdentify($html);

        for ($i = count($p)-1; $i >= 0; $i-=2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
            $reportHtml = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
        }

        $pdf = PDF::loadHtml($html);
	$pdf->save('storage/pdf/ncome.pdf');
        return new JsonResponse([
                "url" => url("storage/pdf/ncome.pdf")
            ],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function outcomesPDF(ExpenseFilters $expenseFilters)
    {
        $expenses = Expense::with('project')->filterBy($expenseFilters)->get();

        $data = [
            'title' => 'El-Saleh Expense Report',
            'date' => date('m/d/Y'),
            'expenses' => $expenses,
        ];
$html = view('expense',['data'=>$data])->render(); 
//	$html = '<h1>مرحبا بكم فى العالم </h1>';
 $pdfarr = [
		'title'=>'اهلا بكم ',
		'data'=>$html, // render file blade with content html
		'header'=>['show'=>false], // header content
		'footer'=>['show'=>false], // Footer content
		'font'=>'aealarabiya', //  dejavusans, aefurat ,aealarabiya ,times
		'font-size'=>12, // font-size 
		'text'=>'', //Write
		'rtl'=>true, //true or false 
		'creator'=>'phpanonymous', // creator file - you can remove this key
		'keywords'=>'phpanonymous keywords', // keywords file - you can remove this key
		'subject'=>'phpanonymous subject', // subject file - you can remove this key
		'filename'=>'phpanonymous.pdf', // filename example - invoice.pdf
		'display'=>'download', // stream , download , print
	];

   	PDF::HTML($pdfarr);
	    return new JsonResponse([
                "url" => url("storage/pdf/expense.pdf")
            ], 200);
    }
}
