## setp
### 0. inti
```shell
# ==============================================================================
# Win11 + Laravel Herd 专用：工业3D打印耗材独立站 WP-CLI 一键初始化脚本 (PowerShell)
# 用法：在站点根目录打开 Windows Terminal / PowerShell，运行 .\init-site.ps1
# ==============================================================================

$OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "🚀 开始初始化 WordPress 工业独立站架构 (Win11 + Herd)..." -ForegroundColor Cyan

# 1. 基础设置与固定链接结构（移除 --hard，因为 Herd 使用 Nginx 而非 Apache）
wp option update blogname "Industrial 3D Filament Extrusion Lines"
wp option update blogdescription "15-25m Turnkey Filament Extrusion Systems with ±0.02mm Laser Closed-Loop Control"
wp rewrite structure '/resources/%postname%/'
wp option update category_base 'resources/topic'

# 2. 创建一级核心页面 (Top-Level Pillar Pages) 并清理回车符
$HOME_ID      = (wp post create --post_type=page --post_title="Home" --post_name="home" --post_status=publish --porcelain).Trim()
$PRODUCTS_ID  = (wp post create --post_type=page --post_title="Products" --post_name="products" --post_status=publish --porcelain).Trim()
$SOLUTIONS_ID = (wp post create --post_type=page --post_title="Solutions" --post_name="solutions" --post_status=publish --porcelain).Trim()
$TECH_ID      = (wp post create --post_type=page --post_title="Technologies" --post_name="technologies" --post_status=publish --porcelain).Trim()
$RESOURCES_ID = (wp post create --post_type=page --post_title="Resources" --post_name="resources" --post_status=publish --porcelain).Trim()
$ABOUT_ID     = (wp post create --post_type=page --post_title="About Us" --post_name="about-us" --post_status=publish --porcelain).Trim()
$CONTACT_ID   = (wp post create --post_type=page --post_title="Contact Us" --post_name="contact" --post_status=publish --porcelain).Trim()

wp option update show_on_front 'page'
wp option update page_on_front $HOME_ID

# 3. 创建 /products/ 下的子页面 (Child Pages)
$P_PLA_ID   = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="PLA & PETG Filament Extrusion Line" --post_name="pla-petg-filament-extrusion-line" --post_status=publish --menu_order=1 --porcelain).Trim()
$P_ABS_ID   = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="ABS & HIPS Engineering Filament Line" --post_name="abs-hips-filament-extrusion-line" --post_status=publish --menu_order=2 --porcelain).Trim()
$P_PEEK_ID  = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="PEEK Ultra-High-Temp Filament Line" --post_name="peek-high-temp-filament-line" --post_status=publish --menu_order=3 --porcelain).Trim()
$P_LINE_ID  = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="15-25m Industrial Extrusion Line" --post_name="15-25m-industrial-line" --post_status=publish --menu_order=4 --porcelain).Trim()
$P_AUX_ID   = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="Auxiliary Equipment (Spooler, Laser Gauge, Chiller)" --post_name="auxiliary-equipment" --post_status=publish --menu_order=5 --porcelain).Trim()
$P_SPARE_ID = (wp post create --post_type=page --post_parent=$PRODUCTS_ID --post_title="Screws, Barrels & Spare Parts" --post_name="spare-parts" --post_status=publish --menu_order=6 --porcelain).Trim()

# 4. 创建 /solutions/ 下的子页面
$S_TURNKEY_ID  = (wp post create --post_type=page --post_parent=$SOLUTIONS_ID --post_title="Turnkey Factory Setup" --post_name="turnkey-factory-setup" --post_status=publish --menu_order=1 --porcelain).Trim()
$S_RETROFIT_ID = (wp post create --post_type=page --post_parent=$SOLUTIONS_ID --post_title="Line Upgrade & Retrofit" --post_name="line-upgrade-retrofit" --post_status=publish --menu_order=2 --porcelain).Trim()

# 5. 创建 /technologies/ 下的子页面
$T_LASER_ID   = (wp post create --post_type=page --post_parent=$TECH_ID --post_title="Closed-Loop Laser Diameter Control" --post_name="laser-diameter-control" --post_status=publish --menu_order=1 --porcelain).Trim()
$T_COOLING_ID = (wp post create --post_type=page --post_parent=$TECH_ID --post_title="Multi-Stage Precision Cooling System" --post_name="precision-cooling-system" --post_status=publish --menu_order=2 --porcelain).Trim()
$T_SCREW_ID   = (wp post create --post_type=page --post_parent=$TECH_ID --post_title="In-House CNC Screw & Barrel Design" --post_name="extruder-screw-design" --post_status=publish --menu_order=3 --porcelain).Trim()

# 6. 创建 /about-us/ 与 /contact/ 下的子页面
$A_PROFILE_ID = (wp post create --post_type=page --post_parent=$ABOUT_ID --post_title="Company Profile & Heritage" --post_name="company-profile" --post_status=publish --menu_order=1 --porcelain).Trim()
$A_FACTORY_ID = (wp post create --post_type=page --post_parent=$ABOUT_ID --post_title="CNC & Assembly Factory Tour" --post_name="factory-tour" --post_status=publish --menu_order=2 --porcelain).Trim()
$A_CERT_ID    = (wp post create --post_type=page --post_parent=$ABOUT_ID --post_title="CE & ISO Certifications" --post_name="certifications" --post_status=publish --menu_order=3 --porcelain).Trim()

$C_QUOTE_ID   = (wp post create --post_type=page --post_parent=$CONTACT_ID --post_title="Get a Custom Line Quote" --post_name="get-a-quote" --post_status=publish --menu_order=1 --porcelain).Trim()
$C_SUPPORT_ID = (wp post create --post_type=page --post_parent=$CONTACT_ID --post_title="Engineering & Recipe Support" --post_name="technical-support" --post_status=publish --menu_order=2 --porcelain).Trim()

# 7. 创建 Footer 独立支持与合规页
$FAQS_ID     = (wp post create --post_type=page --post_title="FAQs" --post_name="faqs" --post_status=publish --porcelain).Trim()
$DOWNLOAD_ID = (wp post create --post_type=page --post_title="Download Center" --post_name="download" --post_status=publish --porcelain).Trim()
$PRIVACY_ID  = (wp post create --post_type=page --post_title="Privacy Policy" --post_name="privacy-policy" --post_status=publish --porcelain).Trim()
$TERMS_ID    = (wp post create --post_type=page --post_title="Terms & Conditions" --post_name="terms" --post_status=publish --porcelain).Trim()

# 8. 创建 /resources/ 文章分类 (Post Categories)
$CAT_GUIDES = (wp term create category "Technical Guides & Recipes" --slug="technical-guides-recipes" --description="Extrusion process parameters, barrel temperature profiles, and troubleshooting guides." --porcelain).Trim()
$CAT_VIDEOS = (wp term create category "Video Gallery" --slug="video-gallery" --description="Live factory acceptance tests, laser tolerance runs, and neat spooling demonstrations." --porcelain).Trim()
$CAT_BUYER  = (wp term create category "Buyer's Guide" --slug="buyers-guide" --description="ROI analysis, workshop layout planning, and extruder screw selection guides." --porcelain).Trim()
$CAT_NEWS   = (wp term create category "Company News" --slug="company-news" --description="Global shipments, exhibition updates, and new extrusion R&D breakthroughs." --porcelain).Trim()

# 9. 创建顶部主导航菜单 (Header Navigation) 并绑定层级
wp menu create "Header Primary Menu"
$M_HOME = (wp menu item add-post "Header Primary Menu" $HOME_ID --title="Home" --porcelain).Trim()

$M_PROD = (wp menu item add-post "Header Primary Menu" $PRODUCTS_ID --title="Products" --porcelain).Trim()
wp menu item add-post "Header Primary Menu" $P_PLA_ID --title="PLA / PETG Extrusion Line" --parent-id=$M_PROD
wp menu item add-post "Header Primary Menu" $P_ABS_ID --title="ABS / HIPS Extrusion Line" --parent-id=$M_PROD
wp menu item add-post "Header Primary Menu" $P_PEEK_ID --title="PEEK High-Temp Line" --parent-id=$M_PROD
wp menu item add-post "Header Primary Menu" $P_LINE_ID --title="15-25m Industrial Line" --parent-id=$M_PROD
wp menu item add-post "Header Primary Menu" $P_AUX_ID --title="Auxiliary Equipment" --parent-id=$M_PROD
wp menu item add-post "Header Primary Menu" $P_SPARE_ID --title="Spare Parts" --parent-id=$M_PROD

$M_SOL = (wp menu item add-post "Header Primary Menu" $SOLUTIONS_ID --title="Solutions" --porcelain).Trim()
wp menu item add-post "Header Primary Menu" $S_TURNKEY_ID --title="Turnkey Factory Setup" --parent-id=$M_SOL
wp menu item add-post "Header Primary Menu" $S_RETROFIT_ID --title="Line Upgrade & Retrofit" --parent-id=$M_SOL

$M_TECH = (wp menu item add-post "Header Primary Menu" $TECH_ID --title="Technologies" --porcelain).Trim()
wp menu item add-post "Header Primary Menu" $T_LASER_ID --title="Laser Diameter Control" --parent-id=$M_TECH
wp menu item add-post "Header Primary Menu" $T_COOLING_ID --title="Precision Cooling System" --parent-id=$M_TECH
wp menu item add-post "Header Primary Menu" $T_SCREW_ID --title="Extruder Screw Design" --parent-id=$M_TECH

$M_RES = (wp menu item add-post "Header Primary Menu" $RESOURCES_ID --title="Resources" --porcelain).Trim()
wp menu item add-term "Header Primary Menu" category $CAT_GUIDES --title="Technical Guides & Recipes" --parent-id=$M_RES
wp menu item add-term "Header Primary Menu" category $CAT_VIDEOS --title="Video Gallery" --parent-id=$M_RES
wp menu item add-term "Header Primary Menu" category $CAT_BUYER --title="Buyer's Guide" --parent-id=$M_RES
wp menu item add-term "Header Primary Menu" category $CAT_NEWS --title="Company News" --parent-id=$M_RES

$M_ABOUT = (wp menu item add-post "Header Primary Menu" $ABOUT_ID --title="About Us" --porcelain).Trim()
wp menu item add-post "Header Primary Menu" $A_PROFILE_ID --title="Company Profile" --parent-id=$M_ABOUT
wp menu item add-post "Header Primary Menu" $A_FACTORY_ID --title="Factory Tour" --parent-id=$M_ABOUT
wp menu item add-post "Header Primary Menu" $A_CERT_ID --title="Certifications" --parent-id=$M_ABOUT

$M_CONTACT = (wp menu item add-post "Header Primary Menu" $CONTACT_ID --title="Contact Us" --porcelain).Trim()
wp menu item add-post "Header Primary Menu" $C_QUOTE_ID --title="Get a Quote" --parent-id=$M_CONTACT
wp menu item add-post "Header Primary Menu" $C_SUPPORT_ID --title="Technical Support" --parent-id=$M_CONTACT

wp menu location assign "Header Primary Menu" menu_1

# 10. 创建底部辅助菜单 (Footer Navigation)
wp menu create "Footer Quick Links"
wp menu item add-post "Footer Quick Links" $P_LINE_ID --title="Filament Extrusion Lines"
wp menu item add-post "Footer Quick Links" $P_AUX_ID --title="Auxiliary Equipment"
wp menu item add-post "Footer Quick Links" $S_TURNKEY_ID --title="Turnkey Solutions"
wp menu item add-post "Footer Quick Links" $FAQS_ID --title="FAQs"
wp menu item add-post "Footer Quick Links" $DOWNLOAD_ID --title="Download Center"
wp menu item add-post "Footer Quick Links" $PRIVACY_ID --title="Privacy Policy"
wp menu item add-post "Footer Quick Links" $TERMS_ID --title="Terms & Conditions"
wp menu item add-custom "Footer Quick Links" "Sitemap" "/sitemap_index.xml"
wp menu location assign "Footer Quick Links" footer

# 刷新重写规则
wp rewrite flush
Write-Host "✅ 24个层级页面、4个Resources分类及主次导航菜单已在 Herd 环境创建完毕！" -ForegroundColor Green
```
### 1. run
```shell
powershell -ExecutionPolicy Bypass -File .\init-site.ps1
```
