@extends('layouts.checkout')

@section('content')
 <section class="breadcrumb-section set-bg" data-setbg="{{ asset('frontend/img/breadcrumb.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Booking Cart</h2>
                        <div class="breadcrumb__option">
                            <a href="/">Home</a>
                            <span>Booking Cart</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="shoping-cart spad">
        <div class="container" id="cart">
           
        </div>
    </section>
@endsection
