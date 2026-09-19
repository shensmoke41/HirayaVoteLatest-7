(function () {

    const allowedDomains = [
        "https://hirayaph.onrender.com",
    ];

    const currentHost = window.location.hostname;

    if (!allowedDomains.includes(currentHost)) {

        document.documentElement.innerHTML = `
            <head>
                <title></title>
                <style>
                    html, body {
                        margin:0;
                        padding:0;
                        width:100%;
                        height:100%;
                        background:#fff;
                    }
                </style>
            </head>
            <body></body>
        `;

        throw new Error("Unauthorized domain");
    }

})();
