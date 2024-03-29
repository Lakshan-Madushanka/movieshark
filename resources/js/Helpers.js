export const truncate = (str, limit=10) => (str.length > limit) ? str.slice(0, limit-1) + '&hellip;' : str;

export const capitalizeFirstLetter = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

