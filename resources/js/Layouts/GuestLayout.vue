

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        
        <div class="container">
            <div class="row">
                <nav class="navbar navbar-expand-lg" style="background-color: #e3f2fd;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Claims's Batching</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">How to use</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/Specialty">Specialty</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/Provider">Provider</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/Insurer">Insurer</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/SubmitClaim">Submit Claim</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/ClaimBatch">Batch Claim</a>
                    </li>
                </ul>
                </div>
            </div>
        </nav>
            </div>
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
