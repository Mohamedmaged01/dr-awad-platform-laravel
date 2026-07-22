@extends('layouts.public')

@section('content')
    <x-home.hero :stats="$heroStats" />
    <x-home.services :services="$homeServices" />
    <x-home.about :achievements="$aboutAchievements" :highlights="$aboutHighlights" />
    <x-home.testimonials :testimonials="$testimonials" :stats="$testimonialStats" />
    <x-home.booking :branches="$bookingBranches" :services="$bookingServices" />
    <x-home.faq :faqs="$faqs" />
@endsection
