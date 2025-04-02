<?php require_once('../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $getRewrittenUrl = $_POST['getRewrittenUrl'];
?>
    <section class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="navbar-brand">
                <span class="navbar-title"><?= $getRewrittenUrl ?></span>
                <span class="arrow-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#fff8db">
                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
                        <path d="M0 0h24v24H0z" fill="none" />
                    </svg>
                </span>
            </div>
            <ul class="sidebar-menu">
                <li class="<?= ($getRewrittenUrl == 'Dashboard') ? "active" : "" ?>">
                    <a href="<?= BASEPATH . '/dashboard' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="M513.33-580v-260H840v260H513.33ZM120-446.67V-840h326.67v393.33H120ZM513.33-120v-393.33H840V-120H513.33ZM120-120v-260h326.67v260H120Z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="<?= ($getRewrittenUrl == 'Products') ? "active" : "" ?>">
                    <a href="<?= BASEPATH . '/products' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Z" />
                        </svg>
                        <span>Products</span>
                    </a>
                </li>
                <li class="<?= ($getRewrittenUrl == 'Purchase History') ? "active" : "" ?>">
                    <a href="<?= BASEPATH . '/purchase-order' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="M480-526 243-663l-43 25v42l280 162 280-162v-42l-43-25-237 137ZM440-91 160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v186q-27-13-57.5-20t-62.5-7q-116 0-198 82t-82 198q0 32 6.5 61.5T466-82q-7-2-13.5-3.5T440-91ZM720 0q-83 0-141.5-58.5T520-200q0-83 58.5-141.5T720-400q83 0 141.5 58.5T920-200q0 83-58.5 141.5T720 0Zm20-208v-112h-40v128l86 86 28-28-74-74Z" />
                        </svg>
                        <span>Purchase Orders</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0)">

                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" width="30px" fill="#fff8db" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                            <path d="m19.667,15.667v.333c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2v-.333s0,.333,0,.333h0c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2l1.238-3h10.524l1.238,3c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2h0m-4.667-10c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm7.143,14h-.619c-.673,0-1.306-.18-1.856-.495-.552.315-1.185.495-1.857.495h-.619c-.673,0-1.306-.18-1.857-.495-.551.315-1.184.495-1.856.495h-.619c-.296,0-.581-.042-.857-.108v4.108h11v-4.108c-.277.066-.562.108-.857.108Zm-13.143-4v-.396l.662-1.604h-4.662c-2.761,0-5,2.239-5,5v5h10v-5.338c-.615-.709-1-1.636-1-2.662Z" />
                        </svg>


                        <span>Vendors</span>
                    </a>

                    <ul class="submenu-items">
                        <li class="<?= ($getRewrittenUrl == 'vendor') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/vendors' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="m640-120-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-29 72-24 143t48 135H80Zm600-80q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240ZM400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Z" />
                                </svg>

                                <span>Profiles</span>
                            </a>
                        </li>

                        <li class="<?= ($getRewrittenUrl == 'vendor-invoice') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/vendor-invoice'  ?>">

                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="#fff8db" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="512" height="512">
                                    <path d="M16,18H8v-3h8v3Zm6-11V24H2V3C2,1.343,3.343,0,5,0H15V7h7Zm-16,0h5v-2H6v2Zm0,4h5v-2H6v2Zm12,2H6v7h12v-7ZM17,.586V5h4.414L17,.586Z" />
                                </svg>


                                <span>Vendor's Invoices</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="submenu">
                    <a href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" height="25px" id="Layer_1" fill="#fff8db" data-name="Layer 1" viewBox="0 0 24 24">
                            <path d="m2,2C2,.895,2.895,0,4,0s2,.895,2,2-.895,2-2,2-2-.895-2-2Zm10,7v-1.5c0-.828-.672-1.5-1.5-1.5h-3c-.828,0-1.5.672-1.5,1.5v1.5h6Zm-3-4c1.105,0,2-.895,2-2s-.895-2-2-2-2,.895-2,2,.895,2,2,2Zm5-1c1.105,0,2-.895,2-2S15.105,0,14,0s-2,.895-2,2,.895,2,2,2Zm-10,3.5c0-.98.407-1.864,1.058-2.5h-2.558c-.828,0-1.5.672-1.5,1.5v1.5h3v-.5Zm11.5-2.5h-2.558c.651.636,1.058,1.52,1.058,2.5v.5h3v-1.5c0-.828-.672-1.5-1.5-1.5Zm7.648,3.681c-.515-.469-1.186-.712-1.878-.678-.697.032-1.339.334-1.794.835l-3.541,3.737c.032.21.065.42.065.638,0,2.083-1.555,3.876-3.617,4.17l-4.241.606-.283-1.979,4.241-.606c1.084-.155,1.9-1.097,1.9-2.191,0-1.22-.993-2.213-2.213-2.213H3c-1.654,0-3,1.346-3,3v7c0,1.654,1.346,3,3,3h9.664l10.674-11.655c.948-1.062.862-2.707-.189-3.665Z" />
                        </svg>

                        <span>Customer</span>
                    </a>

                    <ul class="submenu-items">
                        <li class="<?= ($getRewrittenUrl == 'Customer') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/customer' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="#fff8db" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                    <path d="M18,12c-3.314,0-6,2.686-6,6s2.686,6,6,6,6-2.686,6-6-2.686-6-6-6Zm.752,8.44l-.004,.004c-.744,.744-2.058,.746-2.823-.019l-2.182-2.268,1.387-1.441,2.216,2.301,3.614-3.703,1.398,1.43-3.607,3.696ZM3,6C3,2.691,5.691,0,9,0s6,2.691,6,6-2.691,6-6,6S3,9.309,3,6ZM12.721,24H0v-5c0-2.761,2.239-5,5-5h6.079c-.682,1.178-1.079,2.541-1.079,4,0,2.393,1.056,4.534,2.721,6Z" />
                                </svg>

                                <span>Profiles</span>
                            </a>
                        </li>
                        <li class="<?= ($getRewrittenUrl == '') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/quotation' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M160-400v-80h280v80H160Zm0-160v-80h440v80H160Zm0-160v-80h440v80H160Zm360 560v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-380L643-160H520Zm263-224 37-39-37-37-38 38 38 38Z" />
                                </svg>
                                <span>Quotations</span>
                            </a>
                        </li>
                        <li class="<?= ($getRewrittenUrl == 'Invoice') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/invoice' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M240-80q-50 0-85-35t-35-85v-120h120v-560l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v680q0 50-35 85t-85 35H240Zm480-80q17 0 28.5-11.5T760-200v-560H320v440h360v120q0 17 11.5 28.5T720-160ZM360-600v-80h240v80H360Zm0 120v-80h240v80H360Zm320-120q-17 0-28.5-11.5T640-640q0-17 11.5-28.5T680-680q17 0 28.5 11.5T720-640q0 17-11.5 28.5T680-600Zm0 120q-17 0-28.5-11.5T640-520q0-17 11.5-28.5T680-560q17 0 28.5 11.5T720-520q0 17-11.5 28.5T680-480Z" />
                                </svg>
                                <span>Invoices</span>
                            </a>
                        </li>
                        <li class="<?= ($getRewrittenUrl == '') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/delivery-challan' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M240-160q-50 0-85-35t-35-85H40v-440q0-33 23.5-56.5T120-800h560v160h120l120 160v200h-80q0 50-35 85t-85 35q-50 0-85-35t-35-85H360q0 50-35 85t-85 35Zm0-80q17 0 28.5-11.5T280-280q0-17-11.5-28.5T240-320q-17 0-28.5 11.5T200-280q0 17 11.5 28.5T240-240Zm480 0q17 0 28.5-11.5T760-280q0-17-11.5-28.5T720-320q-17 0-28.5 11.5T680-280q0 17 11.5 28.5T720-240Zm-40-200h170l-90-120h-80v120Z" />
                                </svg>
                                <span>Delivery Challans</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" height="25px" viewBox="0 -960 960 960" width="25px" fill="#fff8db">
                            <path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm-40-600h640v-120H160v120Zm200 280h240v-80H360v80Z" />
                        </svg>
                        <span>Inventory</span>
                    </a>
                    <ul class="submenu-items">
                        <li>
                            <a href="<?= BASEPATH . '/inventory' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M620-163 450-333l56-56 114 114 226-226 56 56-282 282Zm220-397h-80v-200h-80v120H280v-120h-80v560h240v80H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h167q11-35 43-57.5t70-22.5q40 0 71.5 22.5T594-840h166q33 0 56.5 23.5T840-760v200ZM480-760q17 0 28.5-11.5T520-800q0-17-11.5-28.5T480-840q-17 0-28.5 11.5T440-800q0 17 11.5 28.5T480-760Z" />
                                </svg>
                                <span>In Stock</span>

                            </a>
                        </li>
                        <li class="<?= ($getRewrittenUrl == 'Out of stock') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/out-of-stock' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M440-600v-120H320v-80h120v-120h80v120h120v80H520v120h-80ZM280-80q-33 0-56.5-23.5T200-160q0-33 23.5-56.5T280-240q33 0 56.5 23.5T360-160q0 33-23.5 56.5T280-80Zm400 0q-33 0-56.5-23.5T600-160q0-33 23.5-56.5T680-240q33 0 56.5 23.5T760-160q0 33-23.5 56.5T680-80ZM40-800v-80h131l170 360h280l156-280h91L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68.5-39t-1.5-79l54-98-144-304H40Z" />
                                </svg>
                                <span>Out Of Stock</span>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="submenu">
                    <a href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="M640-160v-280h160v280H640Zm-240 0v-640h160v640H400Zm-240 0v-440h160v440H160Z" />
                        </svg>
                        <span>Reports</span>
                    </a>
                    <ul class="submenu-items">
                        <li class="">
                            <a href="<?= BASEPATH . '/sales-report' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#e8eaed">
                                    <path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm0-80h400v-80H280v80ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z" />
                                </svg>
                                <span>Sales</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASEPATH . '/price-history' ?>">
                                <svg class="inner-icon" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="m105-233-65-47 200-320 120 140 160-260 109 163q-23 1-43.5 5.5T545-539l-22-33-152 247-121-141-145 233ZM863-40 738-165q-20 14-44.5 21t-50.5 7q-75 0-127.5-52.5T463-317q0-75 52.5-127.5T643-497q75 0 127.5 52.5T823-317q0 26-7 50.5T795-221L920-97l-57 57ZM643-217q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm89-320q-19-8-39.5-13t-42.5-6l205-324 65 47-188 296Z" />
                                </svg>
                                <span>Price</span>

                            </a>
                        </li>
                        <li>
                            <a href="<?= BASEPATH . '/transactions' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M120-160q-33 0-56.5-23.5T40-240v-440h80v440h680v80H120Zm160-160q-33 0-56.5-23.5T200-400v-320q0-33 23.5-56.5T280-800h560q33 0 56.5 23.5T920-720v320q0 33-23.5 56.5T840-320H280Zm80-80q0-33-23.5-56.5T280-480v80h80Zm400 0h80v-80q-33 0-56.5 23.5T760-400Zm-200-40q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35ZM280-640q33 0 56.5-23.5T360-720h-80v80Zm560 0v-80h-80q0 33 23.5 56.5T840-640Z" />
                                </svg>
                                <span>Transactions</span>

                            </a>
                        </li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0)">

                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17-62.5t47-43.5q60-30 124.5-46T480-440q67 0 131.5 16T736-378q30 15 47 43.5t17 62.5v112H160Zm320-400q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm160 228v92h80v-32q0-11-5-20t-15-14q-14-8-29.5-14.5T640-332Zm-240-21v53h160v-53q-20-4-40-5.5t-40-1.5q-20 0-40 1.5t-40 5.5ZM240-240h80v-92q-15 5-30.5 11.5T260-306q-10 5-15 14t-5 20v32Zm400 0H320h320ZM480-640Z" />
                        </svg>
                        <span>Employee</span>
                    </a>

                    <ul class="submenu-items">
                        <li class="<?= ($getRewrittenUrl == '') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/employee' ?>">

                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                                </svg>
                                <span>Accounts</span>
                            </a>
                        </li>
                        <!-- <li class="<?= ($getRewrittenUrl == '') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/employee' ?>">


                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M200-206q54-53 125.5-83.5T480-320q83 0 154.5 30.5T760-206v-514H200v514Zm280-194q-58 0-99-41t-41-99q0-58 41-99t99-41q58 0 99 41t41 99q0 58-41 99t-99 41ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Z" />
                                </svg>
                                <span>Attendance</span>
                            </a>
                        </li>
                        <li class="<?= ($getRewrittenUrl == '') ? "active" : "" ?>">
                            <a href="<?= BASEPATH . '/employee' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff8db">
                                    <path d="M80-200v-560h800v120h-80L600-440H240v80h280L360-200H80Zm160-320h240v-80H240v80Zm280 400v-70l266-266 70 70-266 266h-70Zm360-290-70-70 36-36q5-5 11-5t11 5l48 48q5 5 5 11t-5 11l-36 36Z" />
                                </svg>
                                <span>Payroll</span>
                            </a>
                        </li> -->
                    </ul>
                </li>

                <li class="submenu">
                    <a href="javascript:void(0)">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#fff8db">
                            <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm112-260q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                    <ul class="submenu-items">
                        <li class="">
                            <a href="<?= BASEPATH . '/firm-profile' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#e8eaed">
                                    <path d="M80-120v-650l200-150 200 150v90h400v560H80Zm80-80h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 0h80v-80h-80v80Zm0 480h480v-400H320v400Zm240-240v-80h160v80H560Zm0 160v-80h160v80H560ZM400-440v-80h80v80h-80Zm0 160v-80h80v80h-80Z" />
                                </svg>
                                <span>Firm Profile</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASEPATH . '/role-permission' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#e8eaed">
                                    <path d="M702-480 560-622l57-56 85 85 170-170 56 57-226 226Zm-342 0q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0 260Zm0-340Z" />
                                </svg>
                                <span>Role Permission</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>

        <!-- Page content -->
        <div class="content">
            <header>
                <!-- Hamburger menu for mobile -->
                <div class="menu-toggle" id="mobile-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </header>
        </div>

    </section>

    <script>
        const submenuItems = document.querySelectorAll('.submenu');

        submenuItems.forEach(item => {
            item.addEventListener('click', function() {
                this.classList.toggle('subactive');
            });
        });
    </script>
<?php } ?>