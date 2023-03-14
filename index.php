<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-parent">
        <div class="main-container">
            <div class="top">
                <h1>SVG Repo Downloader</h1>
                <p>Enter your svg link collection</p>
            </div>
            <div class="bot" id="form">
                <div class="input-container">
                    <div class="left">
                        <input type="text" id="svg-repo-input" placeholder="Enter your SVG Repo Collection URL">
                    </div>
                    <div class="right">
                        <div class="circle" id="submit-btn">
                            <img src="icon.svg" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div id="loading">
                <div class="text">
                    <span>Loading...</span>
                </div>
            </div>
            <div id="result">
                <div class="title">
                    <h2>Something Title</h2>
                </div>
                <div class="body">
                    <ul>
                        <li>
                            <div class="left">
                                <p>Page</p>
                            </div>
                            <div class="right"><span id="page">1 / 5</span></div>
                        </li>
                        <li>
                            <div class="left">
                                <p>Total</p>
                            </div>
                            <div class="right"><span id="total">50</span></div>
                        </li>
                    </ul>
                </div>
                <div class="buttons">
                    <div class="text-button download-zip">
                        <div class="text">
                            <span>Download ZIP</span>
                        </div>
                    </div>
                    <div class="text-button fill next-page">
                        <div class="text">
                            <span>Add Next Page</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
const input = document.getElementById("svg-repo-input");
const btn = document.getElementById("submit-btn");
const form = document.getElementById("form");
const result = document.getElementById("result");
const loading = document.getElementById("loading");
const page = document.getElementById("page");
const total = document.getElementById("total");

const downloadZip = document.querySelector(".download-zip");
const nextPage = document.querySelector(".next-page");

let COLLECTIONS = [];
let TITLE = "";
let NEXTURL = "";

function Ajax(url, method, data, callback) {
    const req = new XMLHttpRequest();

    req.open(method, url);

    req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200) {
            callback(req.responseText);
        }
    }

    req.send(data);
}

async function GetSvgs(url) {
    form.style.display = "none";
    loading.style.display = "block";

    const data = await new Promise((resolve) => {
        Ajax(url, "GET", null, resolve);
    });

    const container = document.createElement("DIV");

    container.innerHTML = data;

    const buttonContainer = container.querySelector(".style_pagingCarrier__uf8b7");
    const parent = container.querySelector(".Layout_fixed__CTmNf .style_nodeListing__hHZW9");
    const items = parent.querySelectorAll(".style_Node__7ZTBP img");
    const urls = sources = [...items].map((item) => "https://www.svgrepo.com" + item.getAttribute("src"));

    const divs = buttonContainer.querySelectorAll("div");
    const span = buttonContainer.querySelector("span");
    const nextBtn = divs[1];
    const text = span.innerText.replace("Page", "").trim();
    const indexes = text.split("/").map((n) => parseInt(n));
    const currentIndex = indexes[0];
    const maxIndex = indexes[1];

    const title = container.querySelector(".Layout_fixed__CTmNf h1").innerText;

    COLLECTIONS = [...COLLECTIONS, ...urls];
    TITLE = title.trim();

    result.style.display = "block";
    form.style.display = "none";
    loading.style.display = "none";
    nextPage.style.display = maxIndex <= 1 ? "none" : "block";

    page.innerText = `${currentIndex} / ${maxIndex}`;
    total.innerText = COLLECTIONS.length;

    if (nextBtn) {
        const a = nextBtn.querySelector("a");

        NEXTURL = "https://www.svgrepo.com" + a.getAttribute("href");
    } else {
        NEXTURL = "";
    }
}

function downloadURI(uri, name) {
    var link = document.createElement("a");
    link.download = name;
    link.href = uri;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    delete link;
}

function Reset() {
    COLLECTIONS = [];
    TITLE = "";

    result.style.display = "none";
    form.style.display = "block";
    loading.style.display = "none";
    nextPage.style.display = "none";

    input.value = "";
}

async function DownloadAsZip(collections, title) {
    const data = new FormData();

    data.append("urls", JSON.stringify(collections));
    data.append("title", title);

    result.style.display = "none";
    form.style.display = "none";
    loading.style.display = "block";
    nextPage.style.display = "none";

    Ajax("downloadSVGS.php", "POST", data, function(res) {
        console.log(res)
        const data = JSON.parse(res);

        downloadURI(data.zipPath, data.title);

        Reset();
    });
}


btn.addEventListener("click", function() {
    const link = input.value;



    GetSvgs(link);
});

downloadZip.addEventListener("click", function() {
    DownloadAsZip(COLLECTIONS, TITLE);
})

nextPage.addEventListener("click", function() {
    console.log(NEXTURL)
    GetSvgs(NEXTURL);
})
</script>

</html>