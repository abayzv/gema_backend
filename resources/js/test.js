//call user api jsonplaceholder
const userApi = async () => {
    return fetch("https://jsonplaceholder.typicode.com/users")
        .then((response) => response.json())
        .then((json) => json);
};

//call product api from internet
const getProduct = async () => {
    return fetch("https://api.myjson.com/bins/1fj6z")
        .then((response) => response.json())
        .then((json) => json);
};
