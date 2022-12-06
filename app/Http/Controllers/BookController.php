<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BookController extends Controller
{
    public function index(){
        $books =  Book::with("category","editorial","authors")->get();
        return [
            "error"=> false,
            "message"=> "Succesfull query",
            "data"=> $books
        ];
    }
    public function show($id){
        $books =  Book::where("id",$id)
                ->with("category","editorial","authors")
                ->get();
        return [
            "error"=> false,
            "message"=> "Succesfull query",
            "data"=> $books
        ];
    }

    public function store(Request $request){
        $exist = Book::where('isbn',trim($request->isbn))->exists();
        if ($exist){
            return [
                "error"=> true,
                "message"=> "ISBN Invalid",
                "data"=> ""
            ];
        }
        $book = new Book();
        $book->isbn = trim($request->isbn);
        $book->title = $request->title;
        $book->category_id = $request->category["id"];
        $book->editorial_id = $request->editorial["id"];
        $book->publish_date = Carbon::now();
        $book->save();
        $bookId = $book->id;

        foreach($request->authors as $author){
            $book->authors()->attach($author);
        }
        return [
            "error"=> false,
            "message"=> "The book has been created!",
            "data"=>[
                "book_id"=> $bookId,
                "book"=> $book,
            ]
        ];
    }
    public function update(Request $request, $id){
        $response = ["error" => false,"message" =>"The book has been updated","data"=>[]];
        $book = Book::find($id);
        if(!$book){
            $response["error"] = true;
            $response["message"] = "Book not found!";
            return $response;
        }
        $isBnOwner = Book::where("isbn",$request->isbn)->first();
        if($isBnOwner->id == $book->id){
            $book->isbn = trim($request->isbn);
        }
        $book->title = $request->title;
        $book->category_id = $request->category["id"];
        $book->editorial_id = $request->editorial["id"];
        $book->publish_date = Carbon::now();
        $book->update();
        // Delete
        foreach($book->authors as $author){
            $book->authors()->detach($author->id);
        }
        // Add
        foreach($request->authors as $author){
            $book->authors()->attach($author);
        }
        $response["data"] = $book;
        return $response;
    }
    public function destroy($id){
        $book = Book::find($id);
        if(!$book){
            return [
                "error"=> true,
                "message"=> "book not found",
                "data"=> ""
            ];
        }
        foreach($book->authors as $author){
            $book->authors()->detach($author->id);
        }
        $book->delete();
        return [
            "error"=> false,
            "message"=> "Succesfull query",
            "data"=> ""
        ];
    }
    public function addBookReview(Request $request, $id){
        // add book review
        $book = Book::find($id);
        if(!$book){
            return [
                "error"=> true,
                "message"=> "book not found",
                "data"=> ""
            ];
        }
        $book->reviews()->create([
            "book_id" => $book->id,
            "user_id" => auth()->user()->id,
            "comment" => $request->comment,
            "edited" => false,
        ]);
        return [
            "error"=> false,
            "message"=> "review added",
            "data"=> $request->comment
        ];
    }
    public function getBookReviews($id){
        $book = Book::find($id);
        if(!$book){
            return [
                "error"=> true,
                "message"=> "book not found",
                "data"=> ""
            ];
        }
        $reviews = $book->reviews()->get();
        return [
            "error"=> false,
            "message"=> "Succesfull query",
            "data"=> $reviews
        ];
    }
    public function editBookReview(Request $request, $id){
        try {
            $book = Book::find($id);
            if(!$book){
                return $this->getResponse200("book not found");
            }
            $review = $book->reviews()->find($request->review_id);
            if(!$review){
                return $this->getResponse200("review not found");
            }
            $current = auth()->user();
            if($review->user_id == $current->id){
                $review->comment = $request->comment;
                $review->edited = true;
                $review->update();
                return [
                    "error"=> false,
                    "message"=> "Succesfull query",
                    "data"=> $review
                ];
            }else{
                return $this->getResponse403();
            }
        } catch (Exception $e) {
            return $this->getResponse403();
        }
    }
}
