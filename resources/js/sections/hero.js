import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const desktopMq = window.matchMedia('(min-width: 1024px)');
    const reducedMq = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Video latar hanya dimuat di desktop bila diunggah owner
    const videoHost = el.querySelector('[data-hero-video-host]');
    const videoSrc = videoHost?.dataset.videoSrc;
    if (videoHost && videoSrc && desktopMq.matches) {
        const video = document.createElement('video');
        video.src = videoSrc;
        video.poster = videoHost.dataset.poster || '';
        video.autoplay = true;
        video.loop = true;
        video.muted = true;
        video.playsInline = true;
        video.preload = 'none';
        video.className = 'w-full h-full object-cover';
        videoHost.innerHTML = '';
        videoHost.appendChild(video);
    }

    if (!reducedMq.matches && desktopMq.matches) {
        const media = el.querySelector('[data-hero-media]');
        const wordmark = el.querySelector('[data-hero-wordmark]');

        if (media) {
            const tw = gsap.to(media, {
                yPercent: 15,
                ease: 'none',
                scrollTrigger: {
                    trigger: el,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 0.6,
                },
            });
            cleanups.push(() => tw.scrollTrigger?.kill());
        }

        if (wordmark) {
            const twW = gsap.fromTo(
                wordmark,
                { fontVariationSettings: '"wdth" 75, "wght" 800' },
                {
                    fontVariationSettings: '"wdth" 120, "wght" 800',
                    ease: 'none',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top top',
                        end: 'bottom top',
                        scrub: 0.5,
                    },
                }
            );
            cleanups.push(() => twW.scrollTrigger?.kill());
        }
    }

    return () => {
        cleanups.forEach((fn) => fn());
    };
}
