@extends('layouts.public')

@section('content')
    <x-home.hero />
    <x-home.services :services="$homeServices" />
    <x-home.about :achievements="$aboutAchievements" :highlights="$aboutHighlights" />
    <x-home.testimonials :testimonials="$testimonials" />
    <x-home.booking :branches="$bookingBranches" :services="$bookingServices" />
    <x-home.faq :faqs="$faqs" />
@endsection
