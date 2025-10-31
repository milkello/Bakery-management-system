<!-- THIS IS AN INDEPENDENT FILE THAT HANDLES PAYMENT REQUESTS AND SEND THEM TO REQUESTPAY.PHP -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTN MoMo Sandbox Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .token-info {
            background: linear-gradient(90deg, #10B981, #059669);
        }
        .access-token {
            background: linear-gradient(90deg, #3B82F6, #1D4ED8);
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">MTN MoMo Payment</h1>
            <p class="text-white text-opacity-80">Sandbox Environment</p>
            <div class="flex justify-center mt-4">
                <div class="bg-yellow-500 text-yellow-900 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-flask mr-2"></i> Test Environment
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl card-shadow overflow-hidden">
            <div class="p-8">
                <!-- Payment Form -->
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Payment Request</h2>
                
                <form id="paymentForm" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="phone">
                            Phone Number (MSISDN)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-mobile-alt text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="phone" 
                                class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" 
                                placeholder="46733123450" 
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="amount">
                            Amount (EUR)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-euro-sign text-gray-400"></i>
                            </div>
                            <input 
                                type="number" 
                                id="amount" 
                                class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" 
                                placeholder="5.00" 
                                step="0.01"
                                required
                            >
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center pulse"
                        id="submitBtn"
                    >
                        <i class="fas fa-paper-plane mr-2"></i> Send Payment Request
                    </button>
                </form>

                <!-- access token codes were here -->
                <div>
                    <!-- Access Token Information -->
                    <div class="mt-8">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Access Token Information</h3>
                        <div class="space-y-4">
                            <!-- Token Details -->
                            <div class="token-info text-white rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">Token Type: <span class="font-semibold">access_token</span></p>
                                        <p class="text-sm">Expires in: <span class="font-semibold">3600 seconds</span></p>
                                    </div>
                                    <i class="fas fa-key text-white text-xl"></i>
                                </div>
                            </div>

                            <!-- Actual Access Token -->
                            <div class="access-token text-white rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium mb-2">Access Token:</p>
                                        <div class="bg-black bg-opacity-20 p-3 rounded-lg">
                                            <input type="hidden" name="accessToken" id="accessToken">
                                            <code id="accessTokenValue" class="text-sm break-all font-mono">No token generated</code>
                                        </div>
                                        <div class="mt-2 space-x-2">
                                            <button id="copyTokenBtn" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded transition duration-200">
                                                <i class="fas fa-copy mr-1"></i> Copy Token
                                            </button>
                                        </div>
                                    </div>
                                    <i class="fas fa-shield-alt text-white text-xl ml-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Result Box -->
                <div id="resultBox" class="hidden mt-8 bg-gray-50 p-6 border rounded-xl fade-in">
                    <h3 class="font-semibold text-xl mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-receipt mr-2 text-purple-600"></i> Transaction Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white p-4 rounded-lg border">
                            <p class="text-sm text-gray-500">Reference ID</p>
                            <p id="refId" class="font-mono font-semibold text-gray-800">N/A</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg border">
                            <p class="text-sm text-gray-500">External ID</p>
                            <p id="extId" class="font-mono font-semibold text-gray-800">N/A</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-1">HTTP Code</p>
                        <div id="httpCodeContainer">
                            <span id="httpCode" class="inline-block px-3 py-1 rounded-full text-sm font-medium"></span>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Response</p>
                        <pre id="responseText" class="text-sm bg-white border p-4 rounded-lg overflow-auto max-h-60"></pre>
                    </div>
                </div>

                <!-- Callback Status -->
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Callback Status</h3>
                    <div id="callbackStatus" class="bg-gray-800 text-white rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="pulse bg-yellow-500 rounded-full h-3 w-3 mr-3"></div>
                            <span>Waiting for transaction...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white text-opacity-70 text-sm">
            <p>Powered by MTN MoMo API | Sandbox Environment</p>
        </div>
    </div>

    <script>
    // Copy access token to clipboard
    function copyAccessToken() {
        const tokenElement = document.getElementById('accessTokenValue');
        const tokenText = tokenElement.textContent;
        
        navigator.clipboard.writeText(tokenText).then(() => {
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
            button.classList.add('bg-green-500');
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500');
            }, 2000);
        });
    }

    document.getElementById("paymentForm").addEventListener("submit", async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById("submitBtn");
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        submitBtn.disabled = true;
        
        const phone = document.getElementById("phone").value.trim();
        const amount = document.getElementById("amount").value.trim();

        try {
            const res = await fetch(`requestpay.php?phone=${encodeURIComponent(phone)}&amount=${encodeURIComponent(amount)}`);
            const data = await res.json();

            const resultBox = document.getElementById("resultBox");
            resultBox.classList.remove("hidden");
            resultBox.classList.add("fade-in");
            
            document.getElementById("refId").textContent = data.reference_id || "N/A";
            document.getElementById("extId").textContent = data.external_id || "N/A";

            // Display the access token returned by requestpay.php
            document.getElementById('accessTokenValue').textContent = data.access_token || 'No token';

            // Display HTTP code badge
            const httpCodeElement = document.getElementById('httpCode');
            const httpCode = data.http_code || data.httpCode || 'N/A';
            httpCodeElement.textContent = httpCode;
            if (typeof httpCode === 'number' || !isNaN(parseInt(httpCode))) {
                const code = parseInt(httpCode);
                if (code >= 200 && code < 300) {
                    httpCodeElement.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                } else if (code >= 400) {
                    httpCodeElement.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                } else {
                    httpCodeElement.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
                }
            }

            // Hide the verbose response box and status (we only show the fields)
            document.getElementById('responseText').textContent = '';
            document.getElementById('callbackStatus').innerHTML = `
                <div class="flex items-center">
                    <div class="pulse bg-blue-500 rounded-full h-3 w-3 mr-3"></div>
                    <span>Payment request sent. Reference and token received.</span>
                </div>
            `;
            
        } catch (error) {
            console.error("Error:", error);
            alert("An error occurred while processing your request.");
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    

    // document.getElementById('copyTokenBtn').addEventListener('click', function() {
    // Enable copy token button (token is now returned by requestpay.php)
    document.getElementById('copyTokenBtn').addEventListener('click', function() {
        const token = document.getElementById('accessTokenValue').textContent;
        if (!token || token === 'No token' || token === 'No token generated') return alert('No token to copy');
        navigator.clipboard.writeText(token);
        this.innerHTML = '<i class="fas fa-check mr-1"></i> Copied';
        const btn = this;
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy mr-1"></i> Copy Token', 2000);
    });

    // // Poll transaction status every 5 seconds
    // function startTransactionStatusCheck(refId) {
    //     const interval = setInterval(async () => {
    //         try {
    //             const res = await fetch(`requesttopaytransactionstatus.php?ref=${encodeURIComponent(refId)}`);
    //             const text = await res.text();

    //             let status = "Unknown";
    //             if (text.includes("SUCCESSFUL")) status = "✅ SUCCESSFUL";
    //             else if (text.includes("FAILED")) status = "❌ FAILED";
    //             else if (text.includes("PENDING")) status = "⏳ PENDING";

    //             document.getElementById("callbackStatus").innerHTML = `
    //                 <div class="flex items-center">
    //                     <div class="bg-yellow-500 rounded-full h-3 w-3 mr-3"></div>
    //                     <span class="font-medium">Transaction Status: ${status}</span>
    //                 </div>
    //             `;

    //             if (status.includes("SUCCESSFUL") || status.includes("FAILED")) {
    //                 clearInterval(interval);
    //                 document.getElementById("callbackStatus").innerHTML = `
    //                     <div class="flex items-center">
    //                         <div class="bg-${status.includes("SUCCESSFUL") ? "green" : "red"}-500 rounded-full h-3 w-3 mr-3"></div>
    //                         <span class="font-medium">${status}</span>
    //                     </div>
    //                 `;
    //             }
    //         } catch (err) {
    //             console.error("Status check error:", err);
    //         }
    //     }, 5000);
    // }

    // // Refresh token check (optional)
    // setInterval(() => {
    //     console.log('Token refresh check...');
    // }, 300000);
</script>

</body>
</html>