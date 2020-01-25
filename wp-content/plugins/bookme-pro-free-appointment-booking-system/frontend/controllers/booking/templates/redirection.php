<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="1;url=<?php echo esc_url( $url ) ?>">
    <script type="text/javascript">
        window.location.href = <?php echo json_encode( $url ) ?>;
    </script>
    <title><?php esc_html_e( 'Page Redirection', 'bookme_pro' ) ?></title>
</head>
<body>
<?php printf( esc_html__( 'If you are not redirected automatically, follow the <a href="%s">link</a>.', 'bookme_pro' ), esc_url( $url ) ) ?>
</body>
</html>