import AOS from 'aos';
import 'aos/dist/aos.css';

export function initAOS() {
    AOS.init({
        duration: 800,
        once: true,           // فقط یک بار اجرا شود
        offset: 80,
        easing: 'ease-out-cubic',
        delay: 0,
        anchorPlacement: 'top-bottom',
        disable: () => {
            // غیرفعال روی موبایل (performance)
            return window.innerWidth < 640;
        },
    });
}
