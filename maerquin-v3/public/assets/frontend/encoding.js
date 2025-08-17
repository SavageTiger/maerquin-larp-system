function encodeUnicodeBase64(str) {
    const entityStr = str.replace(/[\u007F-\uFFFF]/g, ch => {
        return `&#${ch.charCodeAt(0)};`;
    });

    return btoa(encodeURIComponent(entityStr).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
}
