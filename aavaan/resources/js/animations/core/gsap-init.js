import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { TextPlugin } from 'gsap/TextPlugin';
import { CustomEase } from 'gsap/CustomEase';

// ثبت plugins
gsap.registerPlugin(ScrollTrigger, TextPlugin, CustomEase);

// easing اختصاصی آوان — نرم و شاعرانه
CustomEase.create('avan-ease', 'M0,0 C0.25,0.1 0.15,1 1,1');
CustomEase.create('avan-bounce', 'M0,0 C0.175,0.885 0.32,1.275 1,1');

// تنظیمات پیش‌فرض GSAP
gsap.defaults({
    ease: 'avan-ease',
    duration: 0.7,
});

// RTL support — مهم برای پروژه فارسی
gsap.config({
    nullTargetWarn: false,
    trialWarn: false,
});

export { gsap, ScrollTrigger, TextPlugin };
