<div id="captcha-main-wrapper">
    <input type="hidden" name="custom_captcha[id]" value="-1">
    <input type="hidden" name="custom_captcha[key]" value="">
    <div class='captcha-img'>
        <h1><i class="fas fa-spinner fa-spin"></i></h1>
    </div>
</div>

@push('js')
<script type="text/javascript">
(function() {
    var captchaId = -1
    
    function generateCaptcha() {
        axios.get("{{ route('captcha.generate') }}")
        .then((response) => {
            captchaId = response.data.id
            $('#captcha-main-wrapper .captcha-img').html("<img src='" + response.data.img + "'>")
            
        })
        .catch((error) => {
            $('#captcha-main-wrapper .captcha-img i').removeClass("fa-spinner fa-spin").addClass("fa-times text-danger")
            $('#captcha-main-wrapper .captcha-img i').attr("title", '{{ __("Captcha.loadingFailed") }}')
        });
    }
    
    $(function() {
        generateCaptcha()
        $('#captcha-main-wrapper .captcha-img').on('click', function(e) {
            var elmOff = $(this).offset()
            var x = (e.pageX - elmOff.left) / $(this).width() * {{ \Captcha\Http\Util\ImageGenerator::$WIDTH }}
            var y = (e.pageY - elmOff.top) / $(this).height() * {{ \Captcha\Http\Util\ImageGenerator::$HEIGHT + \Captcha\Http\Util\ImageGenerator::$DESCRIPTION_SIZE }}
            
            axios.post("{{ route('captcha.try') }}",{
                id: captchaId,
                x: x | 0,
                y: y | 0,
            })
                .then((response) => {
                    if(response.data.hit) {
                        $('#captcha-main-wrapper .captcha-img').html("<h1><i class='fas fa-check-square'></i></h1>")
                        $('#captcha-main-wrapper input[name="custom_captcha[id]"]').val(response.data.id)
                        $('#captcha-main-wrapper input[name="custom_captcha[key]"]').val(response.data.solveKey)
                    } else {
                        $('#captcha-main-wrapper .captcha-img').html("<h1><i class='fas fa-spinner fa-spin'></i></h1>")
                        setTimeout(generateCaptcha(), 1000)
                    }
                })
                .catch((error) => {
                });
        });
    })
})()
</script>
@endpush
