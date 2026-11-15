import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const desktopMq = window.matchMedia('(min-width: 1024px)');
    const reducedMq = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Video latar hanya dimuat di desktop untuk menjaga ringan di perangkat seluler
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
        video.preload = 'metadata';
        video.className = 'w-full h-full object-cover opacity-75';
        videoHost.innerHTML = '';
        videoHost.appendChild(video);
    }

    if (!reducedMq.matches && desktopMq.matches) {
        const media = el.querySelector('[data-hero-media]');
        if (media) {
            const tw = gsap.to(media, {
                yPercent: 12,
                ease: 'none',
                scrollTrigger: {
                    trigger: el,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 0.8,
                },
            });
            cleanups.push(() => tw.scrollTrigger?.kill());
        }
    }

    return () => {
        cleanups.forEach((fn) => fn());
    };
}
