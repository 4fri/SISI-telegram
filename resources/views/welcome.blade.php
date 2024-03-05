@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-7">
                <div class="card">
                    <form action="{{ route('send_message') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-header">
                            <label class="title" for="">Send Telegram</label>
                        </div>
                        <div class="card-body">
                            <div class="my-4">
                                <div class="form-group">
                                    <label for="">Tujuan Kirim <span class="text-danger">*</span></label>
                                    <select
                                        class="form-control js-example-basic-multiple @error('send_group') is-invalid @enderror"
                                        name="send_group[]" multiple="multiple" style="width: 100%;">
                                        <option value="" hidden>Pilih Tujuan...</option>
                                        <option value="-1002011654734">Group Gausah Diwaro</option>
                                        <option value="1149012956">My Number</option>
                                    </select>
                                    @error('send_group')
                                        <div class="invalid-feedback">
                                            <strong><small>{{ $message }}</small></strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="my-4">
                                <div class="form-group">
                                    <label for="">Pesan Pengirim </label>
                                    <textarea class="form-control summernote @error('sender_message') is-invalid @enderror" name="sender_message"
                                        id="" cols="30" rows="5"></textarea>
                                    @error('sender_message')
                                        <div class="invalid-feedback">
                                            <strong><small>{{ $message }}</small></strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="my-4">
                                <div class="form-group">
                                    <label for="">File Report <span class="text-danger">*</span></label>
                                    <input class="form-control @error('sender_file') is-invalid @enderror" type="file"
                                        name="sender_file">
                                    <span class="text-muted"><small>File berupa png,jpg,jpeg,pdf ukuran maks
                                            2mb</small></span>
                                    @error('sender_file')
                                        <div class="invalid-feedback">
                                            <strong><small>{{ $message }}</small></strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
