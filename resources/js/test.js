//create notification with reactJs
const notification = (message, type) => {
    return {
        message,
        type,
        createdAt: new Date().getTime(),
    };
};
