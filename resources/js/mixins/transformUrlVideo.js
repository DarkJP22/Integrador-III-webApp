/* eslint-disable no-useless-escape */
export default{
    methods: {
        convertEmbedUrl(input) {
            var patternYoutube = /(?:http?s?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(\S+)/g;
            var patternVimeo = /(?:http?s?:\/\/)?(?:www\.)?(?:vimeo\.com)\/?(\S+)/g;
            var patternVideo = /([-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?(?:webm|mp4|ogv))/gi;

            if (patternYoutube.test(input)) {
                const replacement = '<iframe width="420" height="245" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
                input = input.replace(patternYoutube, replacement);
                // For start time, turn get param & into ?
                input = input.replace('&amp;t=', '?t=');

                return input;
            }
            if (patternVimeo.test(input)) {
                const replacement = '<iframe width="420" height="245" src="//player.vimeo.com/video/$1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                input = input.replace(patternVimeo, replacement);

                return input;
            }
            if (patternVideo.test(input)) {
                const replacement = '<video controls="" loop="" controls src="$1" style="max-width: 960px; max-height: 676px;"></video>';
                input = input.replace(patternVideo, replacement);
                return input;
            }

            return input;
        }
    }


};