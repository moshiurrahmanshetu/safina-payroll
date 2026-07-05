<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','Inventory Reports')</title>
    <script src="{{ ltrim(asset('public/js/jquery.min.js'), '/') }}"></script>
    <script src="{{ ltrim(asset('public/js/bootstrap.min.js'), '/') }}"></script>
    <link rel="stylesheet" href="{{ ltrim(asset('public/css/pdf.css'), '/') }}" media="all" />
    <style type="text/css">
      @media screen {
      table.header {
        display: none;
      }
      img.page-footer{
        display: none;
      }
    }
    @media print {
      table.header {
        position: fixed;
        top: 0;
      }
      img.page-footer{
        position: fixed;
        bottom: 0;
      }
      .content-body{
        padding-top:140px !important;
        position: relative;
      }
    }
    </style>
    @yield('css')
  </head>
  @php echo "<script>window.print();</script>" @endphp
  <body class="a4_paper_size_portait">
    <main>
      @include('a4_report_header')
      <table class='a4_center_table_portait content-body'>
        <tr>
          <td width="100%">
            <p class="header_text_align_center">
              @yield('title','Inventory Reports')
            </p>
          </td>
        </tr>
      </table>
      <!-- main content section start -->
        @yield('content','No Content Found')
      <!-- main content section end -->
      @include('a4_report_footer')
    </main>
    <!-- only custom script section start -->
    @yield('script')
  <!-- only custom script section start --> 
  </body>
</html>