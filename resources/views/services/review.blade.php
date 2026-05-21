@extends('layouts.app')

@section('title', $service->name . ' – Reviews | Get My Serv')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
 
@endpush

@section('content')

    {{-- Banner (copied from services/index) --}}
    <section class="about-banner ban-service">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about-banner-content">
                        <h2>{{ $service->name }}</h2>
                        <h4>
                            <a href="{{ url('/') }}">home</a>
                            <span>// <a href="{{ route('categories.index') }}">Services</a></span>
                            @if ($service->category)
                                <span>// <a href="{{ route('categories.index', ['category' => $service->category->id]) }}">{{ $service->category->name }}</a></span>
                            @endif
                            <span>// {{ $service->name }}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="review-page-wrap">
    <div class="container">
        <div class="row g-4">

            {{-- ── Left column: profile-box + review form ── --}}
            <div class="col-lg-8">

                {{-- Profile Box --}}
                <div class="review-profile-box">
                    <div class="profile-box">
                        <div class="text-div">
                            <div class="profile-hds">
                                <h4>{{ $service->name }}</h4>
                            </div>

                            @if ($service->has_packages && $service->basic_package_price)
                                <div class="price">
                                    <h2>AED {{ number_format($service->basic_package_price, 0) }}
                                        @if ($service->basic_package_actual_price)
                                            <span><s>AED {{ number_format($service->basic_package_actual_price, 0) }}</s></span>
                                        @endif
                                    </h2>
                                </div>
                                @if ($service->basic_package_discount_type)
                                    <div class="offer-div">
                                        <div class="offer-details">
                                            @if ($service->basic_package_discount_type === 'percentage')
                                                <h1>Up to {{ number_format($service->basic_package_discount_value, 0) }}% off</h1>
                                            @else
                                                <h1>AED {{ number_format($service->basic_package_discount_value, 0) }} off</h1>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="offer-div" style="visibility:hidden;">
                                        <div class="offer-details"><h1>&nbsp;</h1></div>
                                    </div>
                                @endif
                            @else
                                <div class="price" style="visibility:hidden;"><h2>&nbsp;</h2></div>
                                <div class="offer-div" style="visibility:hidden;">
                                    <div class="offer-details"><h1>&nbsp;</h1></div>
                                </div>
                            @endif

                            <div class="btn-div">
                                @if (in_array($service->service_type, ['book_now', 'both']))
                                    <div class="buttn">
                                        <a href="{{ route('book.create', $service) }}">
                                            Book Now<i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endif
                                @if (in_array($service->service_type, ['enquire_now', 'both']))
                                    <div class="buttn">
                                        <a href="{{ route('enquiry.create', $service) }}">
                                            Enquire Now<i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rating">
                            <div class="rating-details">
                                <ul>
                                    @if ($service->average_rating)
                                        <li>
                                            <i class="fas fa-star" style="color: rgb(17, 172, 86);"></i>
                                            <span>{{ $service->average_rating }}</span>
                                            ({{ $service->review_count }}+)
                                        </li>
                                    @endif
                                    @if ($service->estimate_value)
                                        <li>
                                            <i class="far fa-clock" style="color: rgb(178, 178, 187);"></i>
                                            {{ $service->estimate_value }} {{ ucfirst($service->estimate_unit) }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Review Form --}}
                @auth
                    <div class="review-form-card">
                        <h5>Rate & Review This Service</h5>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('reviews.store', $service) }}"
                              enctype="multipart/form-data" id="review-form">
                            @csrf

                            {{-- Star selector --}}
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Your Rating</label>
                            </div>
                            <div class="star-rating-input mb-3">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating"
                                           value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
                                @endfor
                            </div>

                            {{-- Quill editor --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your Review</label>
                                <div id="review-editor"></div>
                                <input type="hidden" name="comment" id="review-comment"
                                       value="{{ old('comment') }}">
                            </div>

                            {{-- File attachments --}}
                            <div class="mb-4">
                                <div class="attachments-label">Attachments <span class="fw-normal text-muted">(optional – images, PDF, DOC; max 5 MB each)</span></div>
                                <input type="file" name="attachments[]" multiple
                                       class="form-control form-control-sm"
                                       accept="image/*,.pdf,.doc,.docx">
                            </div>

                            <button type="submit" class="btn-submit-review">Submit Review</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info mt-2">
                        <a href="{{ route('login') }}">Log in</a> to write a review for this service.
                    </div>
                @endauth
            </div>

            {{-- ── Right column: all reviews ── --}}
            <div class="col-lg-4">
                <div class="reviews-panel-card">
                    <h5>Customer Reviews</h5>

                    @if ($service->average_rating)
                        <div class="overall-rating mb-3">
                            <div class="big-score">{{ $service->average_rating }}</div>
                            <div class="big-stars mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($service->average_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <div class="review-count">{{ $service->review_count }} review{{ $service->review_count !== 1 ? 's' : '' }}</div>
                        </div>
                    @endif

                    @forelse ($reviews as $review)
                        <div class="review-item">
                            <div class="reviewer-name">{{ $review->user->name }}</div>
                            <div class="review-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            @if ($review->comment)
                                <div class="review-body">{!! $review->comment !!}</div>
                            @endif
                            @if ($review->attachments)
                                <div class="review-attachments">
                                    @foreach ($review->attachments as $file)
                                        <a href="{{ $file }}" target="_blank" rel="noopener">
                                            <i class="fas fa-paperclip"></i> Attachment
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                        </div>
                    @empty
                        <div class="no-reviews">No reviews yet. Be the first!</div>
                    @endforelse

                    @if ($reviews->hasMorePages())
                        <div class="text-center mt-3">
                            <a href="{{ $reviews->nextPageUrl() }}" class="more-reviews-link">
                                More reviews <i class="fas fa-chevron-down ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var editorEl = document.getElementById('review-editor');
    if (!editorEl) return;

    var quill = new Quill('#review-editor', {
        theme: 'snow',
        placeholder: 'Share your experience…',
        modules: {
            toolbar: {
                container: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean'],
                ],
                handlers: {
                    image: function () {
                        var input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.click();
                        input.onchange = function () {
                            var file = input.files[0];
                            if (!file) return;
                            var fd = new FormData();
                            fd.append('image', file);
                            fd.append('_token', '{{ csrf_token() }}');
                            fetch('{{ route("reviews.upload-image") }}', { method: 'POST', body: fd })
                                .then(function (r) { return r.json(); })
                                .then(function (data) {
                                    var range = quill.getSelection(true);
                                    quill.insertEmbed(range.index, 'image', data.url);
                                });
                        };
                    }
                }
            }
        }
    });

    // Restore old value on validation failure
    var oldComment = document.getElementById('review-comment').value;
    if (oldComment) quill.root.innerHTML = oldComment;

    document.getElementById('review-form').addEventListener('submit', function () {
        document.getElementById('review-comment').value = quill.root.innerHTML === '<p><br></p>'
            ? '' : quill.root.innerHTML;
    });
}());
</script>
@endpush
