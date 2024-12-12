(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var animationType = sshaSettings.animationType || 'snowfall';
        var count = sshaSettings.count || 100;
        var speed = sshaSettings.speed || 1;
        var showSlider = sshaSettings.showSlider || false;

        var canvas = document.createElement('canvas');
        canvas.id = 'ssha_canvas';
        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '999999';
        document.body.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        var width = window.innerWidth;
        var height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;

        window.addEventListener('resize', function () {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        });

        var elements = [];
        var angle = 0;

        function initElements() {
            elements = [];
            if (animationType === 'snowfall') {
                for (var i = 0; i < count; i++) {
                    elements.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        r: Math.random() * 3 + 1,
                        d: Math.random() + 1
                    });
                }
            } else if (animationType === 'twinkle') {
                for (var j = 0; j < count; j++) {
                    elements.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        r: Math.random() * 3 + 2,
                        opacity: Math.random(),
                        fade: Math.random() * 0.02 * speed
                    });
                }
            }
        }

        initElements();

        function drawSnow() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = 'rgba(255,255,255,0.8)';
            ctx.beginPath();
            for (var i = 0; i < elements.length; i++) {
                var f = elements[i];
                ctx.moveTo(f.x, f.y);
                ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2, true);
            }
            ctx.fill();
            moveSnow();
        }

        function moveSnow() {
            angle += 0.01 * speed;
            for (var i = 0; i < elements.length; i++) {
                var f = elements[i];
                f.y += (Math.pow(f.d, 2) + 1) * speed;
                f.x += Math.sin(angle) * 2 * speed;

                if (f.y > height) {
                    elements[i] = {
                        x: Math.random() * width,
                        y: 0,
                        r: f.r,
                        d: f.d
                    };
                }
            }
        }

        function drawTwinkle() {
            ctx.clearRect(0, 0, width, height);
            for (var i = 0; i < elements.length; i++) {
                var l = elements[i];
                ctx.beginPath();
                ctx.fillStyle = 'rgba(255,255,204,' + l.opacity + ')';
                ctx.arc(l.x, l.y, l.r, 0, Math.PI * 2, false);
                ctx.fill();

                l.opacity += l.fade;
                if (l.opacity <= 0) {
                    l.opacity = 0;
                    l.fade = Math.abs(l.fade);
                } else if (l.opacity >= 1) {
                    l.opacity = 1;
                    l.fade = -l.fade;
                }
            }
        }

        function animate() {
            requestAnimationFrame(animate);
            if (animationType === 'snowfall') {
                drawSnow();
            } else if (animationType === 'twinkle') {
                drawTwinkle();
            }
        }

        animate();

        // Front-end Slider Control
        if (showSlider) {
            var sliderContainer = document.createElement('div');
            sliderContainer.id = 'ssha_slider_container';

            var sliderLabel = document.createElement('label');
            sliderLabel.textContent = 'Animation Density';
            sliderLabel.setAttribute('for', 'ssha_slider');

            var sliderInput = document.createElement('input');
            sliderInput.type = 'range';
            sliderInput.min = '1';
            sliderInput.max = '1000';
            sliderInput.value = count;
            sliderInput.id = 'ssha_slider';

            sliderInput.addEventListener('input', function () {
                count = parseInt(this.value, 10);
                initElements();
            });

            sliderContainer.appendChild(sliderLabel);
            sliderContainer.appendChild(sliderInput);
            document.body.appendChild(sliderContainer);
        }
    });
})();
