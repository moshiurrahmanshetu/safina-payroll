
  @if($site_info->pdf_no_header_footer==0)
    <img class="page-footer" src="{{asset('storage/app/admin/site_settings/'.$site_info->pdf_footer_img)}}" alt="Logo">
  @endif