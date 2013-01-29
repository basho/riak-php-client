<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:output method="html" />

	<xsl:template name="phpname">
		<xsl:param name="filename" />
		<xsl:variable name="file" select="translate($filename, '\','/')" />
		<xsl:choose>
			<xsl:when test="contains($file, '/src/') = true()">
				<xsl:value-of select="substring-after($file, '/src/')" />
			</xsl:when>
			<xsl:when test="contains($file, '/classes/') = true()">
				<xsl:value-of select="substring-after($file, '/classes/')" />
			</xsl:when>
			<xsl:when test="contains($file, '/source/') = true()">
				<xsl:value-of select="substring-after($file, '/source/')" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$file" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
