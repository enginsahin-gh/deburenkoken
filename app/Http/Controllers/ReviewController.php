<?php

namespace App\Http\Controllers;

use App\Dtos\ReviewDto;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\ReviewRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    private Request $request;

    private ReviewRepository $reviewRepository;

    private OrderRepository $orderRepository;

    public function __construct(
        Request $request,
        ReviewRepository $reviewRepository,
        OrderRepository $orderRepository
    ) {
        $this->request = $request;
        $this->reviewRepository = $reviewRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getReviewView(
        string $orderUuid,
        string $clientUuid
    ): View {
        /** @var Order $order */
        $order = $this->orderRepository->find($orderUuid);

        if (
            ! is_null($order) &&
            ! $order->review()->exists()
        ) {
            $review = $this->reviewRepository->create(
                new ReviewDto(
                    $order,
                    $clientUuid,
                    false,
                    $this->request->query('rating'),
                    ''
                )
            );

            return view('customer.review', [
                'orderUuid' => $orderUuid,
                'rating' => $this->request->query('rating'),
                'hideMenu' => true,
                'review' => $review,
            ]);
        }

        return view('customer.review-submit', ['hideMenu' => true]);
    }

    public function submitReview(string $orderUuid)
    {
        if ($this->request->all() === [] || ! $this->request->isMethod('post')) {
            return redirect()->route('home')->with('error', 'Direct access not allowed.');
        }

        $this->request->validate([
            'reviewText' => [
                'required',
                'string',
                'max:500',
                'regex:/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]*$/',
            ],
        ], [
            'reviewText.required' => 'Een review tekst is verplicht.',
            'reviewText.max' => 'De review mag maximaal 500 tekens bevatten.',
            'reviewText.regex' => 'De review bevat niet-toegestane tekens.',
        ]);

        $review = $this->reviewRepository->find($this->request->input('reviewUuid'));

        $this->reviewRepository->update(
            new ReviewDto(
                $this->orderRepository->find($orderUuid),
                $review->getClientUuid(),
                false,
                $review->getRating(),
                strip_tags($this->request->input('reviewText')),
                null
            ),
            $review->getUuid()
        );

        return view('customer.review-submit', [
            'hideMenu' => true,
        ]);
    }
}
